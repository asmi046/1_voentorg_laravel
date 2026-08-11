<?php

namespace App\Console\Commands;

use App\Services\CdekService;
use Illuminate\Console\Command;

class CdekAvailableTariffs extends Command
{
    protected $signature = 'cdek:available-tariffs --from_code=699 --to_code=141 --weight=2 --json
                            {--from_code= : Код города отправления}
                            {--from_lat= : Широта отправления}
                            {--from_lon= : Долгота отправления}
                            {--from_address= : Адрес отправления}
                            {--to_code= : Код города назначения}
                            {--to_lat= : Широта назначения}
                            {--to_lon= : Долгота назначения}
                            {--to_address= : Адрес назначения}
                            {--weight= : Вес посылки (кг)}
                            {--length= : Длина посылки (см)}
                            {--width= : Ширина посылки (см)}
                            {--height= : Высота посылки (см)}
                            {--date= : Дата планируемой отправки}
                            {--type= : Тип доставки}
                            {--currency= : Валюта}
                            {--tariff_code= : Код тарифа}
                            {--json : Вывод в формате JSON}';

    protected $description = 'Получить доступные тарифы СДЭК (для отладки)';

    public function handle(CdekService $cdek): int
    {
        $packages = [];
        if ($this->option('weight')) {
            $packages[] = array_filter([
                'weight' => (float) $this->option('weight'),
                'length' => $this->option('length') ? (int) $this->option('length') : null,
                'width' => $this->option('width') ? (int) $this->option('width') : null,
                'height' => $this->option('height') ? (int) $this->option('height') : null,
            ]);
        }

        $data = [
            'from_location' => array_filter([
                'code' => $this->option('from_code'),
                'address' => $this->option('from_address'),
                'coordinates' => array_filter([
                    'latitude' => $this->option('from_lat'),
                    'longitude' => $this->option('from_lon'),
                ]),
            ]),
            'to_location' => array_filter([
                'code' => $this->option('to_code'),
                'address' => $this->option('to_address'),
                'coordinates' => array_filter([
                    'latitude' => $this->option('to_lat'),
                    'longitude' => $this->option('to_lon'),
                ]),
            ]),
            'packages' => $packages,
        ];

        $optionalParams = array_filter([
            'date' => $this->option('date'),
            'type' => $this->option('type'),
            'currency' => $this->option('currency'),
            'tariff_code' => $this->option('tariff_code'),
        ]);

        $data = array_merge($data, $optionalParams);

        if (empty($data['from_location']) || empty($data['to_location'])) {
            $this->error('Необходимо указать параметры отправления и назначения (--from_code или --from_lat/--from_lon, и --to_code или --to_lat/--to_lon)');

            return self::FAILURE;
        }

        $tariffs = $cdek->getAvailableTariffs($data);

        if (is_null($tariffs)) {
            $this->error('Не удалось получить список тарифов. Подробности в логах.');

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line(json_encode($tariffs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $headers = ['tariff_code', 'tariff_name', 'delivery_sum', 'period_min', 'period_max', 'calendar_min', 'calendar_max'];
            $rows = [];
            foreach ($tariffs as $tariff) {
                $rows[] = [
                    $tariff['tariff_code'] ?? '',
                    $tariff['tariff_name'] ?? '',
                    $tariff['delivery_sum'] ?? '',
                    $tariff['period_min'] ?? '',
                    $tariff['period_max'] ?? '',
                    $tariff['calendar_min'] ?? '',
                    $tariff['calendar_max'] ?? '',
                ];
            }

            $this->table($headers, $rows);
        }

        return self::SUCCESS;
    }
}
