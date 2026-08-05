<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DetermineProductDimensions extends Command
{
    protected $signature = 'products:determine-dimensions
                            {--limit= : Количество товаров для обработки (0 = все)}
                            {--output= : Путь к выходному JSON файлу}
                            {--sku= : Обработать конкретный товар по SKU}';

    protected $description = 'Определить вес и габариты товаров через AI (Ollama)';

    private string $promptTemplate;
    private array $results = [];

    public function __construct()
    {
        parent::__construct();
        $this->promptTemplate = file_get_contents(base_path('prompts/product_dimensions.txt'));
    }

    public function handle(): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : 0;
        $sku = $this->option('sku');
        $outputPath = $this->option('output') ?: storage_path('app/product_dimensions.json');

        $query = Product::query();

        if ($sku) {
            $query->where('sku', $sku);
        } elseif ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get(['id', 'sku', 'title']);

        if ($products->isEmpty()) {
            $this->error('Товары не найдены');

            return self::FAILURE;
        }

        $this->info("Найдено товаров: {$products->count()}");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $dimensions = $this->getDimensionsFromAI($product->title);

            if ($dimensions) {
                $this->results[$product->sku] = [
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'title' => $product->title,
                    'dimensions' => $dimensions,
                    'determined_at' => now()->toIso8601String(),
                ];
            }

            $bar->advance();
            usleep(100000);
        }

        $bar->finish();
        $this->newLine(2);

        file_put_contents($outputPath, json_encode($this->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Результаты сохранены в: {$outputPath}");
        $this->info("Обработано товаров: " . count($this->results));

        return self::SUCCESS;
    }

    private function getDimensionsFromAI(string $title): ?array
    {
        $prompt = str_replace('{title}', $title, $this->promptTemplate);

        try {
            $http = Http::timeout(config('ollama.timeout'));

            if (config('ollama.api_key')) {
                $http->withHeader('Authorization', 'Bearer ' . config('ollama.api_key'));
            }

            $response = $http->post(config('ollama.base_url') . '/api/generate', [
                'model' => config('ollama.model'),
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json',
            ]);

            if (!$response->successful()) {
                $this->newLine();
                $this->warn("Ошибка API для SKU: {$title} - " . $response->status() . ' ' . $response->body());

                return null;
            }

            $data = $response->json();
            $responseText = $data['response'] ?? '';

            $jsonStart = strpos($responseText, '{');
            $jsonEnd = strrpos($responseText, '}');

            if ($jsonStart === false || $jsonEnd === false) {
                return null;
            }

            $jsonString = substr($responseText, $jsonStart, $jsonEnd - $jsonStart + 1);
            $dimensions = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $dimensions;
        } catch (\Exception $e) {
            $this->newLine();
            $this->warn("Ошибка для SKU: {$title} - " . $e->getMessage());

            return null;
        }
    }
}
