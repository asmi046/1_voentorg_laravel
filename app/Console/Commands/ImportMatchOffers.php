<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductPrices;
use App\Services\Import\ImportProductMatcher;
use App\Services\Import\ModifierExtractor;
use App\Services\Import\NameNormalizer;
use App\Services\Import\TitleSimilarity;
use App\Services\Import\XmlImportIndex;
use Illuminate\Console\Command;

class ImportMatchOffers extends Command
{
    protected $signature = 'import:match-offers
                            {xml : Путь к import0_1.xml}
                            {--dry-run : Только отчёт, без записи в БД}
                            {--threshold=0.7 : Минимальный score для fuzzy-матча}
                            {--auto-accept=0.85 : Score, при котором матч считается уверенным}
                            {--only-category= : Внешний UUID (1С) категории из XML}
                            {--limit= : Ограничить число обрабатываемых товаров}
                            {--only-empty : Обрабатывать только товары без штрихкодов в product_prices}';

    protected $description = 'Сопоставление офферов из CommerceML import0_1.xml с существующими products';

    public function handle(): int
    {
        ini_set('memory_limit', '1G');

        $path = (string) $this->argument('xml');
        if (! is_file($path)) {
            $this->error("Файл не найден: {$path}");

            return self::FAILURE;
        }

        $fuzzyThreshold = (float) $this->option('threshold');
        $autoAccept = (float) $this->option('auto-accept');
        $onlyCategory = $this->option('only-category');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $onlyEmpty = (bool) $this->option('only-empty');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Шаг 1: построение индекса из XML…');

        $extractor = new ModifierExtractor;
        $normalizer = new NameNormalizer;
        $similarity = new TitleSimilarity;
        $index = new XmlImportIndex($extractor, $normalizer);

        $progress1 = $this->output->createProgressBar();
        $progress1->setFormat(' %current% записей [%bar%] %elapsed:6s%');
        $progress1->setRedrawFrequency(500);
        $progress1->start();

        $index->build($path, $onlyCategory, function (int $read) use ($progress1) {
            $progress1->advance();
            if ($read % 5000 === 0) {
                $progress1->setMessage(" обработано $read");
            }
        });

        $progress1->finish();
        $this->newLine();

        $this->line('  Товаров в XML: '.$index->total);
        $this->line('  Не распарсено (без явного модификатора): '.$index->unparsed);
        $this->line('  Уникальных базовых названий: '.count($index->byNormBase));
        $this->line('  Уникальных штрихкодов: '.count($index->byBarcode));

        $matcher = new ImportProductMatcher($normalizer, $similarity, $fuzzyThreshold, $autoAccept);

        $this->info('Шаг 2: сопоставление с товарами БД…');

        $stats = [
            'exact' => 0,
            'external_id' => 0,
            'fuzzy_auto' => 0,
            'fuzzy_ambiguous' => 0,
            'not_found' => 0,
            'created' => 0,
            'updated' => 0,
            'sku_rewritten' => 0,
            'barcode_conflict' => 0,
            'skip_no_barcode' => 0,
        ];

        $conflicts = [];
        $ambiguous = [];

        $query = Product::query()->with(['product_prices', 'tovar_categories']);

        if ($onlyEmpty) {
            $query->whereDoesntHave('product_prices', function ($q) {
                $q->whereNotNull('sku')->where('sku', '!=', '');
            });
        }

        $totalProducts = $limit !== null
            ? min((clone $query)->count(), $limit)
            : (clone $query)->count();

        $count = 0;

        $bar2 = $this->output->createProgressBar($totalProducts ?: 1);
        $bar2->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ETA %estimated:-6s%');
        $bar2->setRedrawFrequency(50);
        $bar2->start();

        $query->chunkById(200, function ($products) use (&$stats, &$conflicts, &$ambiguous, $matcher, $index, $limit, &$count, $dryRun, $bar2) {
            foreach ($products as $product) {
                if ($limit !== null && $count >= $limit) {
                    return false;
                }
                $count++;
                $bar2->advance();

                $categoryExternalIds = $product->tovar_categories
                    ? $product->tovar_categories->pluck('external_id')->filter()->values()->all()
                    : [];

                $result = $matcher->findCandidates($product, $index, $categoryExternalIds);

                if ($result === null) {
                    $stats['not_found']++;

                    continue;
                }

                if ($result->method === 'exact') {
                    $stats['exact']++;
                } elseif ($result->method === 'external_id') {
                    $stats['external_id']++;
                } elseif ($result->score >= $matcher->fuzzyThreshold()) {
                    $stats['fuzzy_auto']++;
                } else {
                    $stats['fuzzy_ambiguous']++;
                }

                if ($result->method === 'fuzzy' && $result->score < $matcher->autoAccept()) {
                    $ambiguous[] = [
                        'product_id' => $product->id,
                        'product_title' => $product->title,
                        'matched_base' => $result->matchedBaseName,
                        'score' => round($result->score, 3),
                        'offers_count' => count($result->offers),
                    ];

                    continue;
                }

                foreach ($result->offers as $offer) {
                    $action = $this->upsertOffer($product, $offer, $dryRun, $conflicts);
                    $stats[$action] = ($stats[$action] ?? 0) + 1;
                }
            }

            return true;
        });

        $bar2->finish();
        $this->newLine();

        $this->newLine();
        $this->info('Результаты сопоставления:');
        foreach ($stats as $k => $v) {
            $this->line('  '.str_pad($k, 18).' : '.$v);
        }

        if ($conflicts) {
            $this->newLine();
            $this->warn('Конфликты штрихкодов ('.count($conflicts).'):');
            $this->table(
                ['product_id', 'product_title', 'sku', 'modifier', 'existing_product_id'],
                array_slice($conflicts, 0, 50),
            );
        }

        if ($ambiguous) {
            $this->newLine();
            $this->warn('Сомнительные матчи ('.count($ambiguous).'):');
            $this->table(
                ['product_id', 'product_title', 'matched_base', 'score', 'offers_count'],
                array_slice($ambiguous, 0, 50),
            );
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('--dry-run: изменения в БД не записывались.');
        }

        return self::SUCCESS;
    }

    private function upsertOffer(Product $product, array $offer, bool $dryRun, array &$conflicts): string
    {
        $barcode = trim((string) $offer['barcode']);
        $modifier = trim((string) $offer['modifier']);

        if ($barcode === '') {
            return 'skip_no_barcode';
        }

        $byBarcode = ProductPrices::where('sku', $barcode)->first();
        if ($byBarcode && $byBarcode->product_id !== $product->id) {
            $conflicts[] = [
                'product_id' => $product->id,
                'product_title' => $product->title,
                'sku' => $barcode,
                'modifier' => $modifier,
                'existing_product_id' => $byBarcode->product_id,
            ];

            return 'barcode_conflict';
        }

        $row = ProductPrices::firstOrNew([
            'product_id' => $product->id,
            'value' => $modifier,
        ]);

        $wasNew = ! $row->exists;
        $skuChanged = $row->exists && $row->sku && $row->sku !== $barcode;

        $row->sku = $barcode;

        if (! $dryRun) {
            $row->save();
        }

        return $wasNew ? 'created' : ($skuChanged ? 'sku_rewritten' : 'updated');
    }
}
