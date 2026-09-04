<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PriceUpdate extends Command
{
    protected $signature = 'price:update
                            {xml=public/shopbase/webdata/offers0_1.xml : Путь к offers0_1.xml}
                            {--skip-parse : Только обновление product_prices из import_offers_data (без парсинга XML)}';

    protected $description = 'Обновление цен и остатков из offers0_1.xml через временную таблицу import_offers_data';

    public function handle(): int
    {
        ini_set('memory_limit', '1G');

        $path = (string) $this->argument('xml');
        if (!$this->option('skip-parse') && !is_file($path)) {
            $this->error("Файл не найден: {$path}");
            return self::FAILURE;
        }

        $stats = [
            'parsed'           => 0,
            'skipped_no_id'    => 0,
            'updated'          => 0,
            'unchanged'        => 0,
        ];

        if (!$this->option('skip-parse')) {
            $this->parseOffers($path, $stats);
        } else {
            $this->info('Шаг 1: пропущен (--skip-parse)');
            $this->newLine();
            $existing = DB::table('import_offers_data')->count();
            $this->line("  В import_offers_data сейчас: {$existing} записей");
        }

        $this->updateProductPrices($stats);

        $this->newLine();
        $this->info('Итоги:');
        foreach ($stats as $k => $v) {
            $this->line("  ".str_pad($k, 18).' : '.$v);
        }

        return self::SUCCESS;
    }

    private function parseOffers(string $path, array &$stats): void
    {
        $this->info('Шаг 1: подготовка import_offers_data…');
        DB::table('import_offers_data')->truncate();
        $this->line('  таблица import_offers_data очищена');

        $this->info('Шаг 2: подсчёт <Предложение> в файле…');
        $total = $this->countOffers($path);
        $this->line("  Всего предложений в XML: {$total}");

        $this->info('Шаг 3: парсинг и вставка…');

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ETA %estimated:-6s%");
        $bar->setRedrawFrequency(200);
        $bar->start();

        $reader = new \XMLReader();
        $reader->open($path);

        $batch = [];
        $batchSize = 1000;

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->localName !== 'Предложение') {
                continue;
            }

            $xml = $reader->readOuterXml();
            if ($xml === '') {
                $bar->advance();
                continue;
            }
            $node = @simplexml_load_string($xml);
            if (!$node) {
                $bar->advance();
                continue;
            }

            $extId = trim((string) ($node->Ид ?? ''));
            if ($extId === '') {
                $stats['skipped_no_id']++;
                $bar->advance();
                continue;
            }

            $name = trim((string) ($node->Наименование ?? ''));

            $price = 0.0;
            if (isset($node->Цены->Цена->ЦенаЗаЕдиницу)) {
                $price = (float) (string) $node->Цены->Цена->ЦенаЗаЕдиницу;
            }

            $count = (int) (string) ($node->Количество ?? 0);

            $batch[] = [
                'ext_id'     => $extId,
                'name'       => $name,
                'price'      => $price,
                'count'      => $count,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                $this->flushOffersBatch($batch, $stats);
                $batch = [];
            }

            $bar->advance();
        }
        $reader->close();

        if ($batch) {
            $this->flushOffersBatch($batch, $stats);
            $batch = [];
        }

        $bar->finish();
        $this->newLine();
    }

    private function flushOffersBatch(array &$batch, array &$stats): void
    {
        try {
            DB::table('import_offers_data')->insert($batch);
            $stats['parsed'] += count($batch);
        } catch (\Throwable $e) {
            foreach ($batch as $row) {
                try {
                    DB::table('import_offers_data')->insert([$row]);
                    $stats['parsed']++;
                } catch (\Throwable $rowErr) {
                    $stats['skipped_no_id']++;
                }
            }
        }
    }

    private function countOffers(string $path): int
    {
        $reader = new \XMLReader();
        $reader->open($path);
        $n = 0;
        while ($reader->read()) {
            if ($reader->nodeType === \XMLReader::ELEMENT && $reader->localName === 'Предложение') {
                $n++;
            }
        }
        $reader->close();
        return $n;
    }

    private function updateProductPrices(array &$stats): void
    {
        $this->info('Шаг 4: обновление product_prices по ext_id…');

        $total = DB::table('product_prices')->count();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ETA %estimated:-6s%");
        $bar->setRedrawFrequency(100);
        $bar->start();

        DB::table('product_prices')
            ->select(['id', 'ext_id', 'price', 'count'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$stats, $bar) {
                foreach ($rows as $row) {
                    if (!$row->ext_id) {
                        $stats['unchanged']++;
                        $bar->advance();
                        continue;
                    }

                    $offer = DB::table('import_offers_data')
                        ->where('ext_id', $row->ext_id)
                        ->first(['price', 'count']);

                    if (!$offer) {
                        $stats['unchanged']++;
                        $bar->advance();
                        continue;
                    }

                    if ((float) $row->price === (float) $offer->price && (int) $row->count === (int) $offer->count) {
                        $stats['unchanged']++;
                        $bar->advance();
                        continue;
                    }

                    DB::table('product_prices')->where('id', $row->id)->update([
                        'price'      => $offer->price,
                        'count'      => $offer->count,
                        'updated_at' => now(),
                    ]);
                    $stats['updated']++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
    }
}
