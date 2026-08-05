<?php

namespace App\Console\Commands;

use App\Services\CdekService;
use Illuminate\Console\Command;

class CdekAllTariffs extends Command
{
    protected $signature = 'cdek:all-tariffs
                            {--currency= : Валюта (RUB, USD, EUR)}
                            {--lang= : Язык (rus, eng)}
                            {--json : Вывод в формате JSON}';

    protected $description = 'Получить все доступные тарифы СДЭК (для отладки)';

    public function handle(CdekService $cdek): int
    {
        $params = array_filter([
            'currency' => $this->option('currency'),
            'lang' => $this->option('lang'),
        ]);

        $tariffs = $cdek->getAllTariffs($params);

        if (is_null($tariffs)) {
            $this->error('Не удалось получить список всех тарифов. Подробности в логах.');

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line(json_encode($tariffs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $headers = ['tariff_code', 'tariff_name', 'description', 'delivery_mode', 'cargo_type'];
            $rows = [];
            foreach ($tariffs as $tariff) {
                $rows[] = [
                    $tariff['tariff_code'] ?? '',
                    $tariff['tariff_name'] ?? '',
                    $tariff['description'] ?? '',
                    $tariff['delivery_mode'] ?? '',
                    $tariff['cargo_type'] ?? '',
                ];
            }

            $this->table($headers, $rows);
        }

        return self::SUCCESS;
    }
}