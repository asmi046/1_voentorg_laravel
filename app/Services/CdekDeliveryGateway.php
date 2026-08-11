<?php

namespace App\Services;

use App\Contracts\DeliveryGateway;

class CdekDeliveryGateway implements DeliveryGateway
{
    public function __construct(private CdekService $cdekService)
    {
    }

    public function listPoints(array $context): array
    {
        $params = array_filter([
            'city_code' => $context['city_code'] ?? null,
            'city' => $context['city'] ?? null,
            'region_code' => $context['region_code'] ?? null,
            'country_code' => $context['country_code'] ?? null,
            'type_code' => $context['type_code'] ?? null,
            'has_cashless' => $context['has_cashless'] ?? null,
            'has_cash' => $context['has_cash'] ?? null,
            'is_dressing' => $context['is_dressing'] ?? null,
            'allowed_cod' => $context['allowed_cod'] ?? null,
            'weight' => $context['weight'] ?? null,
            'page' => $context['page'] ?? null,
            'size' => $context['size'] ?? null,
        ]);

        $points = $this->cdekService->getDeliveryPoints($params);

        if (! is_array($points)) {
            return [];
        }

        if (array_key_exists('delivery_points', $points) && is_array($points['delivery_points'])) {
            $points = $points['delivery_points'];
        }

        return array_values(array_map(function ($point) {
            return [
                'id' => $point['code'] ?? null,
                'label' => $point['name'] ?? '',
                'city' => $point['location']['city'] ?? $point['city'] ?? '',
                'provider' => 'cdek',
                'raw' => $point,
            ];
        }, array_filter($points, 'is_array')));
    }

    public function quote(array $context): array
    {
        $toCode = $context['to_code'] ?? null;
        $weight = $context['weight'] ?? 0;
        $deliveryMode = (int) ($context['delivery_mode'] ?? 3);

        if (! $toCode) {
            return [];
        }

        $tariff = $this->cdekService->getBestTariffByMode($toCode, (float) $weight, $deliveryMode);

        if (empty($tariff)) {
            return [];
        }

        return [
            'provider' => 'cdek',
            'price' => (int) round((float) ($tariff['delivery_sum'] ?? 0)),
            'eta' => $tariff['delivery_date_range'] ?? null,
            'raw' => $tariff,
        ];
    }
}
