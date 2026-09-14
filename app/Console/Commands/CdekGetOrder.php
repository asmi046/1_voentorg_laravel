<?php

namespace App\Console\Commands;

use App\Services\CdekService;
use Illuminate\Console\Command;

class CdekGetOrder extends Command
{
    protected $signature = 'cdek:get-order {uuid : UUID заказа СДЭК}
                            {--json : Вывод в формате JSON}';

    protected $description = 'Получить заказ СДЭК по UUID (для отладки)';

    public function handle(CdekService $cdek): int
    {
        $uuid = $this->argument('uuid');

        $order = $cdek->getOrder($uuid);

        if (is_null($order)) {
            $this->error('Не удалось получить заказ. Подробности в логах.');

            return self::FAILURE;
        }

        $entity = $order['entity'] ?? [];

        if ($this->option('json')) {
            $this->line(json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->info("Заказ СДЭК: {$uuid}");
        $this->table(
            ['Поле', 'Значение'],
            [
                ['UUID', $entity['uuid'] ?? ''],
                ['Номер СДЭК', $entity['cdek_number'] ?? '—'],
                ['Номер магазина', $entity['number'] ?? ''],
                ['Тариф', $entity['tariff_code'] ?? ''],
                ['Статус заказа', $order['statuses'][0]['code'] ?? ''],
                ['Статус (name)', $order['statuses'][0]['name'] ?? ''],
                ['Дата создания', $order['date_create'] ?? ''],
                ['Стоимость доставки', $entity['delivery_cost'] ?? ''],
                ['Сумма НП', $entity['payment']['value'] ?? '0'],
                ['ПВЗ получения', $entity['delivery_point'] ?? ''],
            ]
        );

        $requests = $order['requests'] ?? [];
        if ($requests !== []) {
            $this->line('');
            $this->info('История запросов:');
            $this->table(
                ['type', 'state', 'date_time', 'errors'],
                array_map(fn ($r) => [
                    $r['type'] ?? '',
                    $r['state'] ?? '',
                    $r['date_time'] ?? '',
                    json_encode($r['errors'] ?? [], JSON_UNESCAPED_UNICODE),
                ], $requests)
            );
        }

        return self::SUCCESS;
    }
}
