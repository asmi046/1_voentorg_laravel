<?php

namespace App\Console\Commands;

use App\Services\CdekService;
use Illuminate\Console\Command;

class CdekCities extends Command
{
    protected $signature = 'cdek:cities
                            {--country= : Код страны (ISO 3166-1 alpha-2)}
                            {--region= : Код региона}
                            {--city= : Название населённого пункта}
                            {--page= : Номер страницы}
                            {--size= : Размер страницы}';

    protected $description = 'Получить список населённых пунктов СДЭК (для отладки)';

    public function handle(CdekService $cdek): int
    {
        $params = array_filter([
            'country_codes' => $this->option('country'),
            'region_code' => $this->option('region'),
            'city' => $this->option('city'),
            'page' => $this->option('page'),
            'size' => $this->option('size'),
        ]);

        $cities = $cdek->getCities($params);

        if (is_null($cities)) {
            $this->error('Не удалось получить список населённых пунктов. Подробности в логах.');

            return self::FAILURE;
        }

        $this->info('Получено записей: '.count($cities));

        $headers = ['code', 'city', 'region', 'country_code', 'postal_code', 'latitude', 'longitude'];
        $rows = [];
        foreach ($cities as $city) {
            $rows[] = [
                $city['code'] ?? '',
                $city['city'] ?? '',
                $city['region'] ?? '',
                $city['country_code'] ?? '',
                $city['postal_code'] ?? '',
                $city['latitude'] ?? '',
                $city['longitude'] ?? '',
            ];
        }

        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
