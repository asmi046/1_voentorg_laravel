<?php

namespace Tests\Unit;

use App\DTO\CheckoutData;
use App\DTO\DeliveryData;
use App\DTO\CheckoutItemData;
use App\Models\Product;
use App\Models\ProductPrices;
use App\Models\ShopOrder;
use App\Services\ShopCartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopCartServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShopCartService $service;
    private ProductPrices $priceRow;
    private Product $product;
    private const SESSION_ID = 'test-session-001';

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ShopCartService::class);

        $this->product = Product::factory()->create([
            'weight' => 2,
        ]);

        $this->priceRow = ProductPrices::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'TEST-SKU-1',
            'price' => 1000,
        ]);
    }

    public function test_add_item_creates_cart_and_captures_price_snapshot(): void
    {
        $item = $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 2);

        $this->assertSame('TEST-SKU-1', $item->product_sku);
        $this->assertSame(2, $item->quantity);
        $this->assertSame('1000.00', (string) $item->price_snapshot);
        $this->assertNotNull($item->raw_data);
        $this->assertEquals(1000, $item->raw_data['price']);
    }

    public function test_add_same_item_increments_quantity(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);
        $item = $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 3);

        $this->assertSame(4, $item->quantity);

        $cart = $this->service->getCartWithItems(self::SESSION_ID);
        $this->assertCount(1, $cart->items);
    }

    public function test_add_different_items_creates_separate_positions(): void
    {
        $priceRow2 = ProductPrices::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'TEST-SKU-2',
            'price' => 500,
        ]);

        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-2', 2);

        $cart = $this->service->getCartWithItems(self::SESSION_ID);
        $this->assertCount(2, $cart->items);
    }

    public function test_update_item_quantity(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);

        $item = $this->service->updateItem(self::SESSION_ID, 'TEST-SKU-1', 5);

        $this->assertNotNull($item);
        $this->assertSame(5, $item->quantity);
    }

    public function test_update_nonexistent_item_returns_null(): void
    {
        $result = $this->service->updateItem(self::SESSION_ID, 'MISSING-SKU', 5);

        $this->assertNull($result);
    }

    public function test_delete_item(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);

        $deleted = $this->service->deleteItem(self::SESSION_ID, 'TEST-SKU-1');

        $this->assertTrue($deleted);
        $cart = $this->service->getCartWithItems(self::SESSION_ID);
        $this->assertCount(0, $cart->items);
    }

    public function test_delete_nonexistent_item_returns_false(): void
    {
        $deleted = $this->service->deleteItem(self::SESSION_ID, 'MISSING-SKU');

        $this->assertFalse($deleted);
    }

    public function test_clear_cart(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 3);

        $result = $this->service->clearCart(self::SESSION_ID);

        $this->assertTrue($result);
        $cart = $this->service->getCartWithItems(self::SESSION_ID);
        $this->assertCount(0, $cart->items);
    }

    public function test_calculate_cart_summ(): void
    {
        $priceRow2 = ProductPrices::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'TEST-SKU-2',
            'price' => 500,
        ]);

        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 2); // 1000 * 2 = 2000
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-2', 1); // 500 * 1 = 500

        $cart = $this->service->getCartWithItems(self::SESSION_ID);

        $this->assertSame(2500.0, $this->service->calculateCartSumm($cart));
    }

    public function test_calculate_parcel_weight_converts_kg_to_grams(): void
    {
        // Product weight = 2 (kg, т.к. < 50) -> 2000 г за единицу
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 3); // 2000 * 3 = 6000

        $cart = $this->service->getCartWithItems(self::SESSION_ID);

        $this->assertSame(6000, $this->service->calculateParcelWeight($cart));
    }

    public function test_get_total_quantity(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 2);
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 3);

        $cart = $this->service->getCartWithItems(self::SESSION_ID);

        $this->assertSame(5, $this->service->getTotalQuantity($cart));
    }

    public function test_checkout_creates_order_with_delivery_and_items(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 2);

        $data = new CheckoutData(
            name: 'Иван Иванов',
            email: 'ivan@example.com',
            phone: '+79991234567',
            comment: 'Тестовый заказ',
            promo_code: 'PROMO10',
            delivery: new DeliveryData(
                provider: 'cdek',
                method: 'courier',
                price: '350',
                tariff: null,
                delivery_date_range: ['min' => '2026-08-15', 'max' => '2026-08-17'],
                city: 'Москва',
                pickup_point_id: null,
                pickup_point_address: null,
                delivery_address: 'ул. Ленина, 10',
                apartment: '25',
                raw_data: null,
            ),
            items: [
                new CheckoutItemData(product_sku: 'TEST-SKU-1', quantity: 2),
            ],
            session_id: self::SESSION_ID,
            user_id: null,
        );

        $order = $this->service->checkout($data);

        // Проверка заказа
        $this->assertInstanceOf(ShopOrder::class, $order);
        $this->assertSame('Иван Иванов', $order->name);
        $this->assertSame('+79991234567', $order->phone);
        $this->assertSame('PROMO10', $order->promo_code);
        $this->assertSame(2000.0, (float) $order->cart_summ); // 1000 * 2
        $this->assertSame(2000.0, (float) $order->total_summ);
        $this->assertSame(self::SESSION_ID, $order->session_id);

        // Проверка доставки
        $this->assertNotNull($order->delivery);
        $this->assertSame('cdek', $order->delivery->provider);
        $this->assertSame('courier', $order->delivery->method);
        $this->assertSame('350.00', (string) $order->delivery->price);
        $this->assertSame('Москва', $order->delivery->city);
        $this->assertSame('ул. Ленина, 10', $order->delivery->delivery_address);
        $this->assertSame('25', $order->delivery->apartment);
        $this->assertSame('2026-08-15', $order->delivery->delivery_date_range['min']);
        $this->assertSame('2026-08-17', $order->delivery->delivery_date_range['max']);

        // Проверка позиций заказа (снапшоты)
        $this->assertCount(1, $order->items);
        $orderItem = $order->items->first();
        $this->assertSame('TEST-SKU-1', $orderItem->product_sku);
        $this->assertSame(2, $orderItem->quantity);
        $this->assertSame('1000.00', (string) $orderItem->price);
        $this->assertSame($this->product->title, $orderItem->product_name);
    }

    public function test_checkout_clears_cart_after_order(): void
    {
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);

        $data = $this->buildMinimalCheckoutData();

        $this->service->checkout($data);

        $cart = $this->service->getCartWithItems(self::SESSION_ID);
        $this->assertCount(0, $cart->items);
    }

    public function test_checkout_wrapped_in_transaction(): void
    {
        // Заказ создаётся, счётчик заказов увеличивается на 1
        $this->service->addItem(self::SESSION_ID, 'TEST-SKU-1', 1);

        $ordersBefore = ShopOrder::count();

        $this->service->checkout($this->buildMinimalCheckoutData());

        $this->assertSame($ordersBefore + 1, ShopOrder::count());
    }

    private function buildMinimalCheckoutData(): CheckoutData
    {
        return new CheckoutData(
            name: 'Тест Тестов',
            email: null,
            phone: '+79990000000',
            comment: null,
            promo_code: null,
            delivery: DeliveryData::fromArray([
                'provider' => 'cdek',
                'method' => 'pickup',
                'price' => 0,
            ]),
            items: [
                new CheckoutItemData(product_sku: 'TEST-SKU-1', quantity: 1),
            ],
            session_id: self::SESSION_ID,
            user_id: null,
        );
    }
}
