<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\Import\ModifierExtractor;
use App\Services\Import\NameNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportParseOffers extends Command
{
    protected $signature = 'import:parse-offers
                            {xml=public/shopbase/webdata/import0_1.xml : Путь к import0_1.xml}
                            {--duplicates= : Путь к JSON-файлу для дублей (по умолчанию storage/app/import_duplicates.json)}
                            {--skip-sync : Только парсинг XML → import_offers_raw, без шага синхронизации product_prices}';

    protected $description = 'Экспериментальный парсер: всё из import0_1.xml в import_offers_raw';

    public function handle(): int
    {
        ini_set('memory_limit', '1G');

        $path = (string) $this->argument('xml');
        if (! is_file($path)) {
            $this->error("Файл не найден: {$path}");

            return self::FAILURE;
        }

        $dupPath = $this->option('duplicates')
            ?: storage_path('app/import_duplicates.json');

        $this->info('Шаг 0: подготовка (truncate таблицы и файла дублей)…');

        DB::table('import_offers_raw')->truncate();
        if (is_file($dupPath)) {
            file_put_contents($dupPath, '[]');
        } else {
            @mkdir(dirname($dupPath), 0775, true);
            file_put_contents($dupPath, '[]');
        }
        $this->line('  таблица import_offers_raw очищена');
        $this->line("  файл дублей: {$dupPath}");

        $this->info('Шаг 1: подсчёт <Товар> в файле…');
        $total = $this->countGoods($path);
        $this->line("  Всего товаров в XML: {$total}");

        $this->info('Шаг 2: парсинг и вставка…');

        $extractor = new ModifierExtractor;
        $normalizer = new NameNormalizer;

        $seen = [];
        $duplicates = [];
        $inserted = 0;
        $skippedNoBarcode = 0;
        $skippedNoSize = 0;
        $batch = [];
        $batchSize = 1000;

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ETA %estimated:-6s%');
        $bar->setRedrawFrequency(100);
        $bar->start();

        $reader = new \XMLReader;
        $reader->open($path);

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->localName !== 'Товар') {
                continue;
            }

            $xml = $reader->readOuterXml();
            if ($xml === '') {
                $bar->advance();

                continue;
            }
            $node = @simplexml_load_string($xml);
            if (! $node) {
                $bar->advance();

                continue;
            }

            $title = trim((string) ($node->Наименование ?? ''));
            foreach ($node->ЗначенияРеквизитов->ЗначениеРеквизита ?? [] as $req) {
                if ((string) $req->Наименование === 'Полное наименование') {
                    $fullName = trim((string) $req->Значение);
                    if ($fullName !== '') {
                        $title = $fullName;
                    }
                }
            }

            $barcode = trim((string) ($node->Штрихкод ?? ''));
            $extId = trim((string) ($node->Ид ?? ''));
            $catId = trim((string) ($node->Группы->Ид ?? ''));

            if ($barcode === '') {
                $skippedNoBarcode++;
                $bar->advance();

                continue;
            }

            if (isset($seen[$barcode])) {
                $duplicates[] = [
                    'barcode' => $barcode,
                    'title' => $title,
                    'size' => null,
                    'ext_id' => $extId,
                    'reason' => 'duplicate_barcode_in_xml',
                ];
                $bar->advance();

                continue;
            }
            $seen[$barcode] = true;

            $split = $extractor->split($title);
            $size = $split['modifier'] ?? '';
            if ($size === '') {
                $skippedNoSize++;
                $titleOut = $title;
                $sizeOut = null;
            } else {
                $titleOut = $split['base_name'];
                $sizeOut = $size;
            }

            $batch[] = [
                'title' => $titleOut,
                'size' => $sizeOut,
                'barcode' => $barcode,
                'ext_id' => $extId,
                'category_id' => $catId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                $this->flushBatch($batch, $duplicates, $inserted);
                $batch = [];
            }

            $bar->advance();
        }
        $reader->close();

        if ($batch) {
            $this->flushBatch($batch, $duplicates, $inserted);
            $batch = [];
        }

        $bar->finish();
        $this->newLine();

        file_put_contents($dupPath, json_encode($duplicates, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->newLine();
        $this->info('Результаты парсинга:');
        $this->line("  Добавлено в import_offers_raw : {$inserted}");
        $this->line("  Пропущено (без штрихкода)    : {$skippedNoBarcode}");
        $this->line("  Без выделенного размера       : {$skippedNoSize}");
        $this->line('  Дублей штрихкодов в XML       : '.count($duplicates));
        $this->line("  Файл дублей                   : {$dupPath}");

        if (! $this->option('skip-sync')) {
            $this->newLine();
            $this->syncProductPrices();
        }

        return self::SUCCESS;
    }

    private function syncProductPrices(): void
    {
        $this->info('Шаг 3: синхронизация product_prices…');

        DB::table('product_prices')->truncate();
        $this->line('  таблица product_prices очищена');

        $totalProducts = Product::query()->count();

        $stats = [
            'skipped_sku_len' => 0,
            'matched_with_size' => 0,
            'created_offers' => 0,
            'not_matched' => 0,
        ];

        $bar = $this->output->createProgressBar($totalProducts);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ETA %estimated:-6s%');
        $bar->setRedrawFrequency(50);
        $bar->start();

        Product::query()->select(['id', 'sku'])->orderBy('id')->chunkById(200, function ($products) use (&$stats, $bar) {
            foreach ($products as $product) {
                $sku = (string) $product->sku;
                if (strlen($sku) !== 13) {
                    $stats['skipped_sku_len']++;
                    $bar->advance();

                    continue;
                }

                $matched = DB::table('import_offers_raw')->where('barcode', $sku)->first();

                if (! $matched) {
                    DB::table('product_prices')->insert([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'ext_id' => null,
                        'category_id' => null,
                        'value' => '-',
                        'count' => 0,
                        'price' => 0,
                        'old_price' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $stats['not_matched']++;
                    $stats['created_offers']++;
                    $bar->advance();

                    continue;
                }

                DB::table('product_prices')->insert([
                    'product_id' => $product->id,
                    'sku' => $matched->barcode,
                    'ext_id' => $matched->ext_id,
                    'category_id' => $matched->category_id,
                    'value' => $matched->size ?? '-',
                    'count' => 0,
                    'price' => 0,
                    'old_price' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $stats['matched_with_size']++;
                $stats['created_offers']++;

                $siblings = DB::table('import_offers_raw')
                    ->where('title', $matched->title)
                    ->where('barcode', '!=', $matched->barcode)
                    ->get();

                foreach ($siblings as $s) {
                    DB::table('product_prices')->insert([
                        'product_id' => $product->id,
                        'sku' => $s->barcode,
                        'ext_id' => $s->ext_id,
                        'category_id' => $s->category_id,
                        'value' => $s->size ?? '-',
                        'count' => 0,
                        'price' => 0,
                        'old_price' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $stats['created_offers']++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->newLine();
        $this->info('Результаты синхронизации:');
        $this->line('  Товаров обработано            : '.($stats['matched_with_size'] + $stats['not_matched'] + $stats['skipped_sku_len']));
        $this->line("  Пропущено (sku != 13 симв.)   : {$stats['skipped_sku_len']}");
        $this->line("  Найдено в import_offers_raw   : {$stats['matched_with_size']}");
        $this->line("  Не найдено (создана заглушка) : {$stats['not_matched']}");
        $this->line("  Заглушек (value=\"-\") в БД    : {$stats['not_matched']}");
        $this->line("  Всего создано product_prices  : {$stats['created_offers']}");
    }

    private function flushBatch(array &$batch, array &$duplicates, int &$inserted): void
    {
        try {
            $affected = DB::table('import_offers_raw')->insert($batch);
            $inserted += count($batch);
        } catch (\Throwable $e) {
            foreach ($batch as $row) {
                try {
                    DB::table('import_offers_raw')->insert([$row]);
                    $inserted++;
                } catch (\Throwable $rowErr) {
                    if (str_contains($rowErr->getMessage(), 'Duplicate')
                        || str_contains($rowErr->getMessage(), 'SQLSTATE[23000]')) {
                        $duplicates[] = [
                            'barcode' => $row['barcode'],
                            'title' => $row['title'],
                            'size' => $row['size'],
                            'ext_id' => $row['ext_id'],
                            'reason' => 'duplicate_barcode',
                        ];
                    }
                }
            }
        }
    }

    private function countGoods(string $path): int
    {
        $reader = new \XMLReader;
        $reader->open($path);
        $n = 0;
        while ($reader->read()) {
            if ($reader->nodeType === \XMLReader::ELEMENT && $reader->localName === 'Товар') {
                $n++;
            }
        }
        $reader->close();

        return $n;
    }
}
