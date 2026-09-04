<?php

namespace Tests\Unit;

use App\Services\DeliveryInfoService;
use PHPUnit\Framework\TestCase;

class DeliveryInfoServiceTest extends TestCase
{
    public function test_it_normalizes_delivery_payload_for_order_and_text(): void
    {
        $service = new DeliveryInfoService;

        $payload = $service->normalizeForOrder([
            'deliveryMethod' => 'Курьер',
            'deliveryType' => 'courier',
            'deliveryPrice' => 350,
            'deliveryDateRange' => ['min' => '2026-08-10', 'max' => '2026-08-11'],
            'deliveryAddress' => 'Ленина 10',
            'apartment' => '25',
        ]);

        $this->assertSame('Курьер', $payload['delivery']);
        $this->assertSame('courier', $payload['delivery_type']);
        $this->assertSame(350.0, $payload['delivery_price']);
        $this->assertSame('Ленина 10', $payload['delivery_info']['deliveryAddress']);

        $text = $service->formatForText([
            'deliveryMethod' => 'Курьер',
            'deliveryPrice' => 350,
            'deliveryAddress' => 'Ленина 10',
            'apartment' => '25',
        ]);

        $this->assertStringContainsString('Курьер', $text);
        $this->assertStringContainsString('350', $text);
        $this->assertStringContainsString('Ленина 10', $text);
    }
}
