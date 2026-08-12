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
            'message' => 'Корзина очищена.',
        ]);
    }

    /**
     * Оформить заказ.
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $data = CheckoutData::fromRequest($request);

        $order = $this->cartService->checkout($data);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
            ],
        ], 201);
    }

    /**
     * Сформировать JSON-ответ с данными корзины и сводкой.
     */
    private function cartResponse(\App\Models\ShopCart $cart, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'data' => array_merge([
                'count' => $this->cartService->getTotalQuantity($cart),
                'cart_summ' => $this->cartService->calculateCartSumm($cart),
                'parcel_weight_grams' => $this->cartService->calculateParcelWeight($cart),
                'items' => $cart->items,
            ], $extra),
        ], []));
    }
}
