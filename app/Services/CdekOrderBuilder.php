<?php

namespace App\Services;

use App\Models\ShopOrder;

class CdekOrderBuilder
{
    public function __construct(
        protected CdekService $cdekService
    ) {}

    public function buildOrderPayload(ShopOrder $order): ?array
    {
        $delivery = $order->delivery;

        if (! $delivery || $delivery->provider !== 'СДЭК') {
            return null;
        }

        $method = $delivery->method;

        $payload = [
            'type' => '1',
            'number' => 'ORD-'.$order->id,
            'tariff_code' => $this->getTariffCode($delivery),
            'comment' => "Заказ #{$order->id}. {$order->name}. {$order->phone}",
            'shipment_point' => config('cdek.shipment_point'),
            'from_location' => $this->buildFromLocation(),
            'sender' => $this->buildSender(),
            'recipient' => $this->buildRecipient($order, $delivery),
            'packages' => $this->buildPackages($order),
        ];

        if ($method === 'pickup_point') {
            $payload['delivery_point'] = $delivery->pickup_point_id;
        } elseif ($method === 'courier') {
            $payload['to_location'] = $this->buildLocation($delivery);
            $payload['delivery_recipient_cost'] = [
                'value' => (float) $delivery->price,
                'vat_sum' => 0,
            ];
        }

        return $payload;
    }

    public function createOrder(ShopOrder $order): ?array
    {
        $payload = $this->buildOrderPayload($order);

        if (! $payload) {
            return null;
        }

        return $this->cdekService->registerOrder($payload);
    }

    protected function getTariffCode($delivery): ?string
    {
        $raw = $delivery->raw_data ?? [];

        if ($delivery->method === 'courier') {
            return $raw['tariff']['tariff_code'] ?? null;
        }

        if ($delivery->method === 'pickup_point') {
            return $raw['tariff']['tariff_code'] ?? null;
        }

        return null;
    }

    protected function buildSender(): array
    {
        return [
            'company' => config('cdek.sender.company'),
            'name' => config('cdek.sender.name'),
            'email' => config('cdek.sender.email'),
            'phones' => [
                ['number' => config('cdek.sender.phone')],
            ],
        ];
    }

    protected function buildFromLocation(): array
    {
        return [
            'code' => config('cdek.sender_city_code'),
        ];
    }

    protected function buildRecipient(ShopOrder $order, $delivery): array
    {
        $recipient = [
            'name' => $order->name,
            'phones' => [
                ['number' => preg_replace('/[^0-9]/', '', $order->phone)],
            ],
        ];

        if ($order->email) {
            $recipient['email'] = $order->email;
        }

        if ($delivery->method === 'courier') {
            $recipient['address'] = [
                'street' => $delivery->street ?? '',
                'house' => $delivery->house ?? '',
                'flat' => $delivery->apartment ?? '',
            ];
        }

        return $recipient;
    }

    protected function buildLocation($delivery): array
    {
        $raw = $delivery->raw_data ?? [];

        return [
            'code' => $raw['selectedCity']['code'] ?? null,
            'address' => [
                'street' => $delivery->street ?? '',
                'house' => $delivery->house ?? '',
                'flat' => $delivery->apartment ?? '',
            ],
        ];
    }

    protected function buildPackages(ShopOrder $order): array
    {
        $items = $order->items;

        $totalWeight = 0;
        $totalValue = 0;

        $cdekItems = [];

        foreach ($items as $item) {
            $weight = $this->getItemWeight($item);
            $totalWeight += $weight;
            $totalValue += $item->price;

            $cdekItems[] = [
                'ware_key' => $item->product_sku,
                'name' => $item->product_name ?? $item->product_title,
                'cost' => (float) $item->price,
                'weight' => round($weight, 3),
                'amount' => $item->quantity,
                'payment' => [
                    'value' => (float) $item->price * $item->quantity,
                    'vat_sum' => 0,
                ],
            ];
        }

        return [
            [
                'number' => 'PKG-'.$order->id,
                'weight' => round($totalWeight / 1000, 3),
                'items' => $cdekItems,
            ],
        ];
    }

    protected function getItemWeight($item): float
    {
        if ($item->weight) {
            return (float) $item->weight;
        }

        return 300.0;
    }
}
