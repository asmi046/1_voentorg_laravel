<?php

namespace App\Services;

use App\Contracts\DeliveryGateway;
use Throwable;

class DeliveryCoordinator
{
    /**
     * @param  array<int, DeliveryGateway>  $gateways
     */
    public function __construct(private array $gateways = []) {}

    public function getPickupPoints(array $context): array
    {
        $points = [];

        foreach ($this->gateways as $gateway) {
            try {
                $points = array_merge($points, $gateway->listPoints($context));
            } catch (Throwable) {
                // Ignore provider failures and continue with the rest.
            }
        }

        return $this->normalizePoints($points);
    }

    public function getCourierOffers(array $context): array
    {
        $offers = [];

        foreach ($this->gateways as $gateway) {
            try {
                $result = $gateway->quote($context);

                if (! empty($result)) {
                    $offers[] = $this->normalizeOffer($result);
                }
            } catch (Throwable) {
                // Ignore provider failures and continue with the rest.
            }
        }

        usort($offers, fn ($left, $right) => (float) $left['price'] <=> (float) $right['price']);

        return [
            'best' => $offers[0] ?? null,
            'alternatives' => $offers,
        ];
    }

    private function normalizePoints(array $points): array
    {
        return array_map(function ($point) {
            return [
                'id' => $point['id'] ?? null,
                'label' => $point['label'] ?? '',
                'city' => $point['city'] ?? '',
                'provider' => $point['provider'] ?? 'unknown',
                'raw' => $point['raw'] ?? $point,
            ];
        }, $points);
    }

    private function normalizeOffer(array $offer): array
    {
        return [
            'provider' => $offer['provider'] ?? 'unknown',
            'price' => (int) ($offer['price'] ?? 0),
            'eta' => $offer['eta'] ?? null,
            'raw' => $offer['raw'] ?? $offer,
        ];
    }
}
