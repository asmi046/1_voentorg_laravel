<?php

namespace App\Services;

class DeliveryInfoService
{
    public function normalizeForOrder(array $payload): array
    {
        $deliveryMethod = $payload['deliveryMethod'] ?? $payload['delivery'] ?? '';
        $deliveryType = $payload['deliveryType'] ?? $payload['delivery_type'] ?? '';
        $deliveryPrice = $this->toFloat($payload['deliveryPrice'] ?? $payload['delivery_price'] ?? null);
        $deliveryDateRange = $payload['deliveryDateRange'] ?? $payload['delivery_date_range'] ?? null;

        $normalized = [
            'delivery' => $deliveryMethod,
            'delivery_type' => $deliveryType,
            'delivery_price' => $deliveryPrice,
            'delivery_date_range' => $deliveryDateRange,
            'delivery_info' => [
                'deliveryMethod' => $deliveryMethod,
                'deliveryType' => $deliveryType,
                'deliveryPrice' => $deliveryPrice,
                'deliveryDateRange' => $deliveryDateRange,
                'deliveryAddress' => $payload['deliveryAddress'] ?? $payload['delivery_address'] ?? '',
                'apartment' => $payload['apartment'] ?? '',
                'selectedPickupPoint' => $payload['selectedPickupPoint'] ?? null,
                'selectedCity' => $payload['selectedCity'] ?? null,
                'transportCompany' => $payload['transportCompany'] ?? null,
                'tariff' => $payload['tariff'] ?? null,
            ],
        ];

        if (! empty($deliveryMethod)) {
            $normalized['delivery_text'] = $this->formatForText($payload);
        }

        return $normalized;
    }

    public function formatForText(array $payload): string
    {
        $deliveryMethod = $payload['deliveryMethod'] ?? $payload['delivery'] ?? 'Не указано';
        $deliveryPrice = $this->toFloat($payload['deliveryPrice'] ?? $payload['delivery_price'] ?? null);
        $deliveryAddress = $payload['deliveryAddress'] ?? $payload['delivery_address'] ?? '';
        $apartment = $payload['apartment'] ?? '';
        $deliveryDateRange = $payload['deliveryDateRange'] ?? $payload['delivery_date_range'] ?? null;

        $parts = [
            "Способ доставки: {$deliveryMethod}",
            "Стоимость: {$deliveryPrice} ₽",
        ];

        if ($deliveryAddress !== '') {
            $address = $apartment !== '' ? $deliveryAddress.', кв. '.$apartment : $deliveryAddress;
            $parts[] = "Адрес: {$address}";
        }

        if ($deliveryDateRange) {
            $parts[] = 'Срок: '.implode(' - ', array_filter([
                $deliveryDateRange['min'] ?? null,
                $deliveryDateRange['max'] ?? null,
            ]));
        }

        return implode("\n", $parts);
    }

    private function toFloat(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) $value;
    }
}
