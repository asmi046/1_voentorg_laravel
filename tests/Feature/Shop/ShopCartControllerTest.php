<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductPrices;
use App\Models\ShopOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopCartControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithShopSession;

    private string $sku;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpShopSession();

        $this->product = Product::factory()->create(['weight' => 1]);
        $this->sku = ProductPrices::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'API-SKU-1',
            'price' => 1500,
        ])->sku;
    }

    public function test_get_empty_cart(): void
    {
        $response = $this->getJson('/shop/cart');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.count', 0);
        $response->assertJsonPath('data.position', []);
    }

    public function test_position_includes_product_data(): void
    {
        $this->postJson('/shop/cart/add', [
            'product_sku' => $this->sku,
            'quantity' => 1,
        ]);

        $response = $this->getJson('/shop/cart');

        $response->assertOk();
        $response->assertJsonPath('data.position.0.product_sku', $this->sku);
        $response->assertJsonPath('data.position.0.quantity', 1);
        $response->assertJsonPath('data.position.0.price', 1500);
        $response->assertJsonPath('data.position.0.product.title', $this->product->title);
        $response->assertJsonPath('data.position.0.product.slug', $this->product->slug);
        $response->assertJsonPath('data.position.0.product.img', $this->product->img);
        $response->assertJsonPath('data.position.0.variant.sku', $this->sku);
    }

    public function test_add_item_to_cart(): void
    {
        $response = $this->postJson('/shop/cart/add', [
            'product_sku' => $this->sku,
            'quantity' => 2,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.count', 2);
        $response->assertJsonPath('data.cart_summ', 3000);

        $this->assertDatabaseHas('shop_cart_items', [
            'product_sku' => $this->sku,
            'quantity' => 2,
        ]);
    }

    public function test_add_item_with_default_quantity(): void
    {
        $response = $this->postJson('/shop/cart/add', [
            'product_sku' => $this->sku,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.count', 1);
    }

    public function test_add_same_item_increments_quantity(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 1]);
        $response = $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 2]);

        $response->assertJsonPath('data.count', 3);
    }

    public function test_add_item_validation_fails_without_sku(): void
    {
        $response = $this->postJson('/shop/cart/add', ['quantity' => 1]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_sku']);
    }

    public function test_add_item_validation_fails_with_invalid_quantity(): void
    {
        $response = $this->postJson('/shop/cart/add', [
            'product_sku' => $this->sku,
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_update_item_quantity(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 1]);

        $response = $this->postJson('/shop/cart/update', [
            'product_sku' => $this->sku,
            'quantity' => 5,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.count', 5);
        $response->assertJsonPath('data.item.quantity', 5);
    }

    public function test_update_nonexistent_item_returns_404(): void
    {
        $response = $this->postJson('/shop/cart/update', [
            'product_sku' => 'MISSING-SKU',
            'quantity' => 1,
        ]);

        $response->assertNotFound();
        $response->assertJsonPath('success', false);
    }

    public function test_delete_item(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 2]);

        $response = $this->deleteJson('/shop/cart/delete', [
            'product_sku' => $this->sku,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.count', 0);
        $response->assertJsonPath('data.position', []);
    }

    public function test_delete_nonexistent_item_returns_404(): void
    {
        $response = $this->deleteJson('/shop/cart/delete', [
            'product_sku' => 'MISSING-SKU',
        ]);

        $response->assertNotFound();
    }

    public function test_clear_cart(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 3]);

        $response = $this->deleteJson('/shop/cart/clear');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $getResponse = $this->getJson('/shop/cart');
        $getResponse->assertJsonPath('data.count', 0);
    }

    public function test_checkout_creates_order(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 2]);

        $response = $this->postJson('/shop/cart/checkout', [
            'name' => 'Пётр Петров',
            'email' => 'petr@example.com',
            'phone' => '+79998887766',
            'comment' => 'Позвонить перед доставкой',
            'promo_code' => 'SAVE10',
            'delivery' => [
                'provider' => 'cdek',
                'method' => 'courier',
                'price' => 350,
                'city' => 'Курск',
                'delivery_address' => 'ул. Мирная, 5',
                'apartment' => '10',
            ],
            'items' => [
                ['product_sku' => $this->sku, 'quantity' => 2],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $orderId = $response->json('data.order_id');

        $order = ShopOrder::find($orderId);
        $this->assertNotNull($order);
        $this->assertSame('Пётр Петров', $order->name);
        $this->assertSame('SAVE10', $order->promo_code);
        $this->assertSame(3000.0, (float) $order->cart_summ); // 1500 * 2
        $this->assertSame('cdek', $order->delivery->provider);
        $this->assertSame('Курск', $order->delivery->city);
        $this->assertCount(1, $order->items);
    }

    public function test_checkout_clears_cart(): void
    {
        $this->postJson('/shop/cart/add', ['product_sku' => $this->sku, 'quantity' => 1]);

        $this->postJson('/shop/cart/checkout', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'delivery' => [
                'method' => 'pickup',
            ],
            'items' => [
                ['product_sku' => $this->sku, 'quantity' => 1],
            ],
        ]);

        $response = $this->getJson('/shop/cart');
        $response->assertJsonPath('data.count', 0);
    }

    public function test_checkout_validation_fails_without_required_fields(): void
    {
        $response = $this->postJson('/shop/cart/checkout', [
            'delivery' => ['method' => 'pickup'],
            'items' => [
                ['product_sku' => $this->sku, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'phone']);
    }

    public function test_checkout_validation_fails_without_items(): void
    {
        $response = $this->postJson('/shop/cart/checkout', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'delivery' => ['method' => 'pickup'],
            'items' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
    }

    public function test_checkout_validation_fails_with_invalid_delivery_method(): void
    {
        $response = $this->postJson('/shop/cart/checkout', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'delivery' => ['method' => 'invalid_method'],
            'items' => [
                ['product_sku' => $this->sku, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['delivery.method']);
    }
}
