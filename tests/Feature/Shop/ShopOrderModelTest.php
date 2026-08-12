<?php

namespace Tests\Feature\Shop;

use App\Models\ShopOrder;
use App\Models\ShopOrderDelivery;
use App\Models\ShopOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_one_delivery(): void
    {
        $order = ShopOrder::create([
            'name' => 'Тест',
            'phone' => '+79990000000',
            'session_id' => 'session-1',
        ]);

        ShopOrderDelivery::create([
            'order_id' => $order->id,
            'provider' => 'cdek',
            'method' => 'courier',
            'price' => 350,
            'city' => 'Москва',
        ]);

        $order = $order->fresh(['delivery']);

        $this->assertNotNull($order->delivery);
        $this->assertSame('cdek', $order->delivery->provider);
        $this->assertSame('350.00', (string) $order->delivery->price);
    }

    public function test_order_has_many_items(): void
    {
        $order = ShopOrder::create([
            'name' => 'Тест',
            'phone' => '+79990000000',
            'session_id' => 'session-2',
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_sku' => 'SKU-1',
            'price' => 1000,
            'quantity' => 2,
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_sku' => 'SKU-2',
            'price' => 500,
            'quantity' => 1,
        ]);

        $order = $order->fresh(['items']);

        $this->assertCount(2, $order->items);
        $this->assertSame('SKU-1', $order->items[0]->product_sku);
        $this->assertSame('SKU-2', $order->items[1]->product_sku);
    }

    public function test_deleting_order_cascades_to_delivery_and_items(): void
    {
        $order = ShopOrder::create([
            'name' => 'Тест',
            'phone' => '+79990000000',
            'session_id' => 'session-3',
        ]);

        ShopOrderDelivery::create([
            'order_id' => $order->id,
            'provider' => 'cdek',
            'method' => 'pickup',
            'price' => 0,
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_sku' => 'SKU-1',
            'price' => 1000,
            'quantity' => 1,
        ]);

        $orderId = $order->id;
        $order->delete();

        $this->assertDatabaseMissing('shop_orders', ['id' => $orderId]);
        $this->assertDatabaseMissing('shop_order_deliveries', ['order_id' => $orderId]);
        $this->assertDatabaseMissing('shop_order_items', ['order_id' => $orderId]);
    }

    public function test_delivery_json_casts(): void
    {
        $order = ShopOrder::create([
            'name' => 'Тест',
            'phone' => '+79990000000',
            'session_id' => 'session-4',
        ]);

        $delivery = ShopOrderDelivery::create([
            'order_id' => $order->id,
            'provider' => 'cdek',
            'method' => 'courier',
            'price' => 400,
            'delivery_date_range' => ['min' => '2026-08-20', 'max' => '2026-08-22'],
            'raw_data' => ['tariff_code' => 137, 'delivery_sum' => 400],
        ]);

        $delivery = $delivery->fresh();

        $this->assertSame('2026-08-20', $delivery->delivery_date_range['min']);
        $this->assertSame('2026-08-22', $delivery->delivery_date_range['max']);
        $this->assertSame(137, $delivery->raw_data['tariff_code']);
    }

    public function test_order_decimal_casts(): void
    {
        $order = ShopOrder::create([
            'name' => 'Тест',
            'phone' => '+79990000000',
            'session_id' => 'session-5',
            'cart_summ' => 1250.50,
            'discount_summ' => 100,
            'total_summ' => 1150.50,
        ]);

        $order = $order->fresh();

        $this->assertSame('1250.50', (string) $order->cart_summ);
        $this->assertSame('100.00', (string) $order->discount_summ);
        $this->assertSame('1150.50', (string) $order->total_summ);
    }
}
