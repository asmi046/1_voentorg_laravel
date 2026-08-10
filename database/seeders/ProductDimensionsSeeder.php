<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductDimensionsSeeder extends Seeder
{
    public function run(): void
    {
        $file = public_path('tmp_content/product_dimensions.json');

        if (!File::exists($file)) {
            $this->command->error("Файл не найден: {$file}");

            return;
        }

        $data = json_decode(File::get($file), true);

        if (!is_array($data)) {
            $this->command->error('Некорректный JSON в файле габаритов.');

            return;
        }

        $updates = [];

        foreach ($data as $key => $item) {
            $sku = $item['sku'] ?? $key;
            $dimensions = $item['dimensions'] ?? null;

            if (!$dimensions) {
                continue;
            }

            $weightKg = $dimensions['weight'] ?? null;

            $updates[(string) $sku] = [
                'weight' => $weightKg !== null ? (int) round((float) $weightKg * 1000) : null,
                'length' => isset($dimensions['length']) ? (int) round((float) $dimensions['length']) : null,
                'width'  => isset($dimensions['width'])  ? (int) round((float) $dimensions['width'])  : null,
                'height' => isset($dimensions['height']) ? (int) round((float) $dimensions['height']) : null,
            ];
        }

        if (empty($updates)) {
            $this->command->warn('В файле габаритов нет данных для загрузки.');

            return;
        }

        $skuToIds = [];
        foreach (DB::table('products')->select(['id', 'sku'])->get() as $product) {
            $skuToIds[(string) $product->sku][] = $product->id;
        }

        $updated = 0;
        $missing = 0;

        DB::transaction(function () use ($updates, $skuToIds, &$updated, &$missing) {
            foreach ($updates as $sku => $fields) {
                $ids = $skuToIds[$sku] ?? null;

                if (!$ids) {
                    $missing++;
                    continue;
                }

                DB::table('products')->whereIn('id', $ids)->update($fields);
                $updated += count($ids);
            }
        });

        $this->command->info(
            "Габариты товаров загружены: обновлено {$updated}, не найдено по sku {$missing}, всего записей " . count($updates) . '.'
        );
    }
}
