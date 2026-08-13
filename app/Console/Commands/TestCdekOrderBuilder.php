<?php

namespace App\Console\Commands;

use App\Models\ShopOrder;
use App\Services\CdekOrderBuilder;
use Illuminate\Console\Command;

class TestCdekOrderBuilder extends Command
{
    protected $signature = 'test:cdek-order-builder';

    protected $description = 'Test CDEK order builder with test data';

    public function __construct(
        private CdekOrderBuilder $builder
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Testing CDEK Order Builder...');
        $this->newLine();

        $testOrders = $this->getTestOrders();

        foreach ($testOrders as $name => $orderData) {
            $this->info("Testing: {$name}");
            $this->line('----------------------------------------');

            $order = $this->createTestOrder($orderData);
            $payload = $this->builder->buildOrderPayload($order);

            if (! $payload) {
                $this->error('Failed to build payload');
                $this->newLine();
                continue;
            }

            $this->info('Payload built successfully:');
            $this->newLine();

            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->newLine();

            $this->line('----------------------------------------');
            $this->newLine();
        }

        return Command::SUCCESS;
    }

    private function createTestOrder(array $data): ShopOrder
    {
        $order = new ShopOrder();

        $order->id = $data['id'];
        $order->name = $data['name'];
        $order->email = $data['email'] ?? null;
        $order->phone = $data['phone'];
        $order->comment = $data['comment'] ?? null;
        $order->promo_code = $data['promo_code'] ?? null;
        $order->cart_summ = $data['cart_summ'];
        $order->discount_summ = $data['discount_summ'] ?? 0;
        $order->total_summ = $data['total_summ'];

        $order->setRelation('delivery', new \App\Models\ShopOrderDelivery($data['delivery']));

        $items = [];
        foreach ($data['items'] as $itemData) {
            $items[] = new \App\Models\ShopOrderItem($itemData);
        }
        $order->setRelation('items', collect($items));

        return $order;
    }

    private function getTestOrders(): array
    {
        return [
            'CDEK Pickup Point' => [
                'id' => 1001,
                'name' => 'Иванов Иван Иванович',
                'email' => 'test@example.com',
                'phone' => '+7 (900) 123-45-67',
                'comment' => 'Позвонить за час',
                'promo_code' => 'TEST2026',
                'cart_summ' => 5000.00,
                'discount_summ' => 500.00,
                'total_summ' => 4500.00,
                'delivery' => [
                    'provider' => 'СДЭК',
                    'method' => 'pickup_point',
                    'price' => 300.00,
                    'tariff' => json_encode([
                        'tariff_code' => 137,
                        'delivery_sum' => 300,
                        'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-17'],
                    ]),
                    'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-17'],
                    'city' => 'Курск',
                    'pickup_point_id' => 'KURSK01',
                    'pickup_point_address' => 'г. Курск, ул. Ленина, д. 1',
                    'raw_data' => [
                        'selectedCity' => [
                            'name' => 'Курск',
                            'code' => 141,
                        ],
                        'tariff' => [
                            'tariff_code' => 137,
                            'delivery_sum' => 300,
                            'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-17'],
                        ],
                    ],
                ],
                'items' => [
                    [
                        'product_sku' => '2000000394954',
                        'product_title' => 'Каркасный тактический рюкзак 50-70л, Олива',
                        'product_name' => 'Рюкзак тактический',
                        'quantity' => 1,
                        'price' => 5000.00,
                        'weight' => 3000,
                    ],
                ],
            ],
            'CDEK Courier' => [
                'id' => 1002,
                'name' => 'Петров Петр Петрович',
                'email' => 'petrov@example.com',
                'phone' => '+7 (900) 987-65-43',
                'comment' => 'Квартира на 3 этаже, домофон не работает',
                'promo_code' => null,
                'cart_summ' => 8000.00,
                'discount_summ' => 0,
                'total_summ' => 8300.00,
                'delivery' => [
                    'provider' => 'СДЭК',
                    'method' => 'courier',
                    'price' => 300.00,
                    'tariff' => json_encode([
                        'tariff_code' => 136,
                        'delivery_sum' => 300,
                        'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-16'],
                    ]),
                    'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-16'],
                    'city' => 'Курск',
                    'street' => 'Ленина',
                    'house' => '10',
                    'apartment' => '25',
                    'raw_data' => [
                        'selectedCity' => [
                            'name' => 'Курск',
                            'code' => 141,
                        ],
                        'tariff' => [
                            'tariff_code' => 136,
                            'delivery_sum' => 300,
                            'delivery_date_range' => ['min' => '2026-08-15', 'max' => '2026-08-16'],
                        ],
                    ],
                ],
                'items' => [
                    [
                        'product_sku' => '2000000547824',
                        'product_title' => 'Толстовка Тактическая СПАРТА (Олива)',
                        'product_name' => 'Толстовка',
                        'quantity' => 2,
                        'price' => 4000.00,
                        'weight' => 700,
                    ],
                ],
            ],
            'Pickup (not CDEK)' => [
                'id' => 1003,
                'name' => 'Сидоров Сидр Сидорович',
                'email' => 'sidorov@example.com',
                'phone' => '+7 (900) 555-55-55',
                'comment' => null,
                'promo_code' => null,
                'cart_summ' => 2000.00,
                'discount_summ' => 0,
                'total_summ' => 2000.00,
                'delivery' => [
                    'provider' => null,
                    'method' => 'pickup',
                    'price' => 0,
                    'tariff' => null,
                    'delivery_date_range' => null,
                    'city' => null,
                    'raw_data' => null,
                ],
                'items' => [
                    [
                        'product_sku' => '2000000000001',
                        'product_title' => 'Товар тестовый',
                        'product_name' => 'Товар',
                        'quantity' => 1,
                        'price' => 2000.00,
                        'weight' => 500,
                    ],
                ],
            ],
        ];
    }
}