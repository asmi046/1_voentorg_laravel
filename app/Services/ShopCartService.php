<?php

namespace App\Services;

use App\DTO\CheckoutData;
use App\DTO\CheckoutItemData;
use App\Events\ShopOrderCreated;
use App\Models\Product;
use App\Models\ProductPrices;
use App\Models\ShopCart;
use App\Models\ShopCartItem;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\DB;

class ShopCartService
{
    private const DEFAULT_WEIGHT_GRAMS = 300;

    /**
     * Получить (или создать) корзину по идентификатору сессии.
     */
    public function getCart(string $sessionId, ?int $userId = null): ShopCart
    {
        return ShopCart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => $userId],
        );
    }

    /**
     * Получить корзину с позициями и обогащёнными данными товаров.
     */
    public function getCartWithItems(string $sessionId, ?int $userId = null): ShopCart
    {
        return $this->getCart($sessionId, $userId)
            ->load(['items.productPrice', 'items.product']);
    }

    /**
     * Добавить товар в корзину (или увеличить количество).
     */
    public function addItem(string $sessionId, string $productSku, int $quantity, ?int $userId = null): ShopCartItem
    {
        $cart = $this->getCart($sessionId, $userId);

        $product = $this->resolveProductBySku($productSku);

        $item = $cart->items()->where('product_sku', $productSku)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            $item->save();
        } else {
            $item = $cart->items()->create([
                'product_sku' => $productSku,
                'quantity' => $quantity,
                'price_snapshot' => $product['price'] ?? null,
                'raw_data' => $product,
            ]);
        }

        return $item->fresh();
    }

    /**
     * Обновить количество позиции.
     */
    public function updateItem(string $sessionId, string $productSku, int $quantity, ?int $userId = null): ?ShopCartItem
    {
        $item = $this->findItem($sessionId, $productSku, $userId);

        if (! $item) {
            return null;
        }

        $item->quantity = $quantity;
        $item->save();

        return $item->fresh();
    }

    /**
     * Удалить позицию из корзины.
     */
    public function deleteItem(string $sessionId, string $productSku, ?int $userId = null): bool
    {
        $item = $this->findItem($sessionId, $productSku, $userId);

        if (! $item) {
            return false;
        }

        return (bool) $item->delete();
    }

    /**
     * Очистить корзину.
     */
    public function clearCart(string $sessionId, ?int $userId = null): bool
    {
        $cart = $this->getCart($sessionId, $userId);

        return (bool) $cart->items()->delete();
    }

    /**
     * Рассчитать сумму корзины (без учёта скидки и доставки).
     */
    public function calculateCartSumm(ShopCart $cart): float
    {
        return (float) $cart->items->reduce(function (float $carry, ShopCartItem $item) {
            $price = $this->resolveItemPrice($item);

            return $carry + ($price * $item->quantity);
        }, 0.0);
    }

    /**
     * Рассчитать общий вес посылки в граммах (для расчёта доставки).
     */
    public function calculateParcelWeight(ShopCart $cart): int
    {
        return (int) $cart->items->reduce(function (int $carry, ShopCartItem $item) {
            $weight = $this->resolveItemWeight($item);

            return $carry + ($weight * $item->quantity);
        }, 0);
    }

    /**
     * Общее количество единиц товара в корзине.
     */
    public function getTotalQuantity(ShopCart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    /**
     * Оформить заказ: создаёт ShopOrder + Delivery + Items + платёж YooKassa в одной транзакции.
     *
     * @return array{order: ShopOrder, payment: object|null}
     */
    public function checkout(CheckoutData $data): array
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->getCart($data->session_id, $data->user_id)->load('items');

            $cartSumm = $this->calculateCartSumm($cart);

            $order = ShopOrder::create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'comment' => $data->comment,
                'promo_code' => $data->promo_code,
                'cart_summ' => $cartSumm,
                'discount_summ' => 0,
                'total_summ' => $cartSumm,
                'session_id' => $data->session_id,
                'user_id' => $data->user_id,
            ]);

            $order->delivery()->create($data->delivery->toAttributes());

            $order->items()->createMany(
                array_map(fn (CheckoutItemData $item) => $this->buildOrderItem($item), $data->items)
            );

            // Регистрируем платёж в YooKassa.
            $yookassa = app(YooKassaService::class);

            $tovars = $order->items->map(fn ($item) => [
                'product_title' => $item->product_title,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ])->all();

            $normalizedTovars = $yookassa->normalizeTovarsForPayment(
                $tovars,
                (float) $cartSumm,
                0
            );

            $payment = $yookassa->registerOrder($order, $normalizedTovars);

            if (! empty($payment) && isset($payment->id)) {
                $order->update(['payment_id' => $payment->id]);
            }

            // Очищаем корзину после успешного оформления заказа.
            $this->clearCart($data->session_id, $data->user_id);

            event(new ShopOrderCreated($order->id, $order));

            return [
                'order' => $order->load(['delivery', 'items']),
                'payment' => $payment,
            ];
        });
    }

    /**
     * Найти позицию в корзине по SKU.
     */
    private function findItem(string $sessionId, string $productSku, ?int $userId): ?ShopCartItem
    {
        $cart = $this->getCart($sessionId, $userId);

        return $cart->items()->where('product_sku', $productSku)->first();
    }

    /**
     * Построить массив атрибутов для позиции заказа (со снапшотом данных товара).
     *
     * @return array<string, mixed>
     */
    private function buildOrderItem(CheckoutItemData $item): array
    {
        $product = $this->resolveProductBySku($item->product_sku);

        return [
            'product_sku' => $item->product_sku,
            'product_name' => $product['title'] ?? null,
            'product_title' => $product['title'] ?? null,
            'price' => $product['price'] ?? 0,
            'quantity' => $item->quantity,
            'weight_grams' => $product['weight_grams'] ?? null,
            'dimensions' => $product['dimensions'] ?? null,
            'raw_data' => $product,
        ];
    }

    /**
     * Получить актуальную цену позиции (из снапшота или из БД).
     */
    private function resolveItemPrice(ShopCartItem $item): float
    {
        if ($item->price_snapshot !== null) {
            return (float) $item->price_snapshot;
        }

        $product = $this->resolveProductBySku($item->product_sku);

        return (float) ($product['price'] ?? 0);
    }

    /**
     * Получить вес позиции в граммах.
     */
    private function resolveItemWeight(ShopCartItem $item): int
    {
        $raw = $item->raw_data ?? [];
        $weight = $raw['weight_grams'] ?? $raw['weight'] ?? null;

        if ($weight === null) {
            return self::DEFAULT_WEIGHT_GRAMS;
        }

        $numeric = (float) $weight;

        // Если значение похоже на килограммы (< 50), переводим в граммы.
        if ($numeric > 0 && $numeric < 50) {
            return (int) round($numeric * 1000);
        }

        return (int) round($numeric);
    }

    /**
     * Разрешить данные товара по SKU из таблиц product_prices и products.
     *
     * @return array<string, mixed>
     */
    private function resolveProductBySku(string $sku): array
    {
        $priceRow = ProductPrices::where('sku', $sku)->first();

        if (! $priceRow) {
            return ['sku' => $sku, 'price' => 0];
        }

        $product = Product::find($priceRow->product_id);

        $weightGrams = null;
        if ($product && $product->weight !== null) {
            $weight = (float) $product->weight;
            $weightGrams = $weight < 50 ? (int) round($weight * 1000) : (int) round($weight);
        }

        return [
            'sku' => $sku,
            'title' => $product?->title,
            'price' => (float) $priceRow->price,
            'weight_grams' => $weightGrams,
            'dimensions' => $product ? array_filter([
                'length' => $product->length,
                'width' => $product->width,
                'height' => $product->height,
            ]) : null,
        ];
    }
}
