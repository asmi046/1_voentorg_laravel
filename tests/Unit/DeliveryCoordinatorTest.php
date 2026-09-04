<?php

namespace Tests\Unit;

use App\Contracts\DeliveryGateway;
use App\Services\DeliveryCoordinator;
use PHPUnit\Framework\TestCase;

class DeliveryCoordinatorTest extends TestCase
{
    public function test_it_normalizes_pickup_points_and_selects_the_cheapest_courier_offer(): void
    {
        $gatewayA = new class implements DeliveryGateway
        {
            public function listPoints(array $context): array
            {
                return [
                    [
                        'id' => 'pvz-1',
                        'label' => 'Пункт 1',
                        'city' => 'Москва',
                        'provider' => 'gateway-a',
                    ],
                ];
            }

            public function quote(array $context): array
            {
                return [
                    'provider' => 'gateway-a',
                    'price' => 250,
                    'eta' => '1-2 дня',
                ];
            }
        };

        $gatewayB = new class implements DeliveryGateway
        {
            public function listPoints(array $context): array
            {
                return [
                    [
                        'id' => 'pvz-2',
                        'label' => 'Пункт 2',
                        'city' => 'Москва',
                        'provider' => 'gateway-b',
                    ],
                ];
            }

            public function quote(array $context): array
            {
                return [
                    'provider' => 'gateway-b',
                    'price' => 120,
                    'eta' => '2-3 дня',
                ];
            }
        };

        $coordinator = new DeliveryCoordinator([$gatewayA, $gatewayB]);

        $points = $coordinator->getPickupPoints(['city' => 'Москва']);

        $this->assertCount(2, $points);
        $this->assertSame('gateway-a', $points[0]['provider']);
        $this->assertSame('Пункт 2', $points[1]['label']);
        $this->assertSame('pvz-2', $points[1]['raw']['id']);

        $offers = $coordinator->getCourierOffers(['city_code' => '1', 'weight' => 2]);

        $this->assertSame('gateway-b', $offers['best']['provider']);
        $this->assertSame(120, $offers['best']['price']);
        $this->assertSame(120, $offers['best']['raw']['price']);
        $this->assertSame(2, count($offers['alternatives']));
        $this->assertSame([120, 250], array_column($offers['alternatives'], 'price'));
    }
}
