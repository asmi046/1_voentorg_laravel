<?php

namespace App\Http\Controllers\Shop;

use App\DTO\CheckoutData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\AddToCartRequest;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Http\Requests\Shop\DeleteCartItemRequest;
use App\Http\Requests\Shop\UpdateCartItemRequest;
use App\Services\ShopCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopCartController extends Controller
{
    public function __construct(private readonly ShopCartService $cartService)
    {
    }

    /**
     * Получить содержимое корзины.
     */
    public function get(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems(
            $request->session()->getId(),
            $request->user()?->id,
        );

        return $this->cartResponse($cart);
    }

    /**
     * Добавить товар в корзину.
     */
    public function add(AddToCartRequest $request): JsonResponse
    {
        $item = $this->cartService->addItem(
            $request->session()->getId(),
            $request->string('product_sku')->toString(),
            (int) $request->input('quantity', 1),
            $request->user()?->id,
        );

        $cart = $this->cartService->getCartWithItems(
            $request->session()->getId(),
            $request->user()?->id,
        );

        return $this->cartResponse($cart, ['item' => $item]);
    }

    /**
     * Обновить количество позиции.
     */
    public function update(UpdateCartItemRequest $request): JsonResponse
    {
        $item = $this->cartService->updateItem(
            $request->session()->getId(),
            $request->string('product_sku')->toString(),
            (int) $request->input('quantity'),
            $request->user()?->id,
        );

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Позиция не найдена в корзине.',
            ], 404);
        }

        $cart = $this->cartService->getCartWithItems(
            $request->session()->getId(),
            $request->user()?->id,
        );

        return $this->cartResponse($cart, ['item' => $item]);
    }

    /**
     * Удалить позицию из корзины.
     */
    public function delete(DeleteCartItemRequest $request): JsonResponse
    {
        $deleted = $this->cartService->deleteItem(
            $request->session()->getId(),
            $request->string('product_sku')->toString(),
            $request->user()?->id,
        );

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Позиция не найдена в корзине.',
            ], 404);
        }

        $cart = $this->cartService->getCartWithItems(
            $request->session()->getId(),
            $request->user()?->id,
        );

        return $this->cartResponse($cart);
    }

    /**
     * Очистить корзину.
     */
    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clearCart(
            $request->session()->getId(),
            $request->user()?->id,
        );

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Корзина очищена.',
            ],
        ]);
    }

    /**
     * Оформить заказ.
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $data = CheckoutData::fromRequest($request);

        $result = $this->cartService->checkout($data);
        $order = $result['order'];
        $payment = $result['payment'];

        $payInfo = null;
        if (! empty($payment) && is_object($payment)) {
            $payInfo = [
                'id' => $payment->id ?? null,
                'status' => $payment->status ?? null,
                'confirmation' => $payment->confirmation ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pay_info' => $payInfo,
                'order_id' => $order->id,
                'order_number' => '№'.$order->id.'_S'.rand(100, 999),
                'tovars' => [],
            ],
        ], 201);
    }

    /**
     * Сформировать JSON-ответ с данными корзины и сводкой.
     */
    private function cartResponse(\App\Models\ShopCart $cart, array $extra = []): JsonResponse
    {
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_sku' => $item->product_sku,
                'product_id' => $item->product?->id,
                'quantity' => $item->quantity,
                'quentity' => $item->quantity,
                'price' => $item->price_snapshot ?? $item->productPrice?->price ?? 0,
                'tovar_content' => $item->product ? [
                    'img' => $item->product->img,
                    'slug' => $item->product->slug,
                    'title' => $item->product->title,
                    'weight' => $item->product->weight,
                ] : null,
                'tovar_data' => [
                    'price' => $item->productPrice?->price ?? 0,
                ],
            ];
        })->all();

        return response()->json(array_merge([
            'success' => true,
            'data' => array_merge([
                'count' => $this->cartService->getTotalQuantity($cart),
                'cart_summ' => $this->cartService->calculateCartSumm($cart),
                'parcel_weight_grams' => $this->cartService->calculateParcelWeight($cart),
                'position' => $items,
            ], $extra),
        ], []));
    }
}
