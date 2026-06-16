<?php

namespace App\Http\Controllers\Cart;

use App\Events\BascetOrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\BascetForm;
use App\Models\Cart;
use App\Models\Order;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index() {
        return view('cart.cart');
    }

    public function add(YooKassaService $pay, Request $request) {
        $product_id = $request->input('product_id');
        $product_sku = $request->input('product_sku');
        $_token = $request->input('_token');
        $addcount = $request->input('addcount');

        Cart::add($product_id, $product_sku, $addcount);

        return array($product_id, $_token);
    }

    public function get_all() {
        $cart_product = Cart::with('tovar_data', 'tovar_content')->where("carts.session_id", session()->getId())->get();
        return ["count" => Cart::cart_coun(), "position" => $cart_product] ;
    }

    public function clear() {
        return Cart::cart_clear();
    }

    public function update(Request $request) {
        $product_id = $request->input('product_id');
        $new_count = $request->input('count');
        return Cart::update_tovar($product_id, $new_count);
    }

    public function delete(Request $request) {
        $product_id = $request->input('product_id');
        return Cart::delete_tovar($product_id);
    }

    public function send(YooKassaService $pay, BascetForm $request) {
        $order = Order::create([
            'name' => $request->input('fio'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'adress' => $request->input('adress'),
            'amount' => $request->input('amount'),
            'base_summ' => $request->input('base_summ'),
            'discount_summ' => $request->input('discount_summ'),
            'comment' => $request->input('comment'),
            'position_count' => $request->input('count'),
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,
        ]);

        foreach ($request->input('tovars') as $item) {
            $order->orderCart()->create($item);
        }

        // $order->orderProducts()->sync(array_column($request->input('tovars'), "id"));

        event(new BascetOrderCreated($order->id, $request->all()));

        $order_number = '№'.$order->id.'_S'.rand(100, 999);

        $resPay = null;
        try {
            $normalizedTovars = $this->normalizeTovarsForPayment(
                $request->input('tovars', []),
                (float) $request->input('base_summ', 0),
                (float) $request->input('discount_summ', 0)
            );

            $resPay = $pay->registerOrder($order, $normalizedTovars);

            if (! empty($resPay) && isset($resPay['id'])) {
                Order::update_order_pay_id($order->id, $resPay['id']);
            }
        } catch (\Exception $e) {
            Log::channel('pay')->error('Ошибка получения платежа: '.$e->getMessage());
        } finally {
            Cart::cart_clear();

            return ['pay_info' => $resPay, 'order_id' => $order->id, 'order_number' => $order_number, 'tovars' => $request->input('tovars')];
        }

        return ['send' => true];
    }

    public function thencs() {
        Cart::cart_clear();
        return view("cart.thencs");
    }

    /**
     * Нормализует позиции для платежной системы так, чтобы сумма товаров
     * совпала с целевой суммой (base_summ - discount_summ).
     *
     * Возвращает позиции в формате quantity=1, чтобы избежать ошибок округления
     * на строках с количеством > 1.
     */
    private function normalizeTovarsForPayment(array $tovars, float $baseSumm, float $discountSumm): array
    {
        if (empty($tovars)) {
            return $tovars;
        }

        $discount = max((int) round($discountSumm), 0);
        if ($discount <= 0) {
            return $tovars;
        }

        $base = max((int) round($baseSumm), 0);
        $targetSum = max($base - $discount, 0);

        $units = [];
        foreach ($tovars as $item) {
            $quantity = max((int) ($item['quentity'] ?? 1), 1);
            $unitPrice = max((int) round((float) ($item['price'] ?? 0)), 0);

            for ($i = 0; $i < $quantity; $i++) {
                $units[] = [
                    'item' => $item,
                    'base_price' => $unitPrice,
                ];
            }
        }

        if (empty($units)) {
            return $tovars;
        }

        $baseUnitsSum = array_sum(array_column($units, 'base_price'));
        if ($baseUnitsSum <= 0) {
            return $tovars;
        }

        // Largest remainder method: гарантирует точную итоговую сумму в рублях.
        $allocated = [];
        $remainders = [];
        $allocatedSum = 0;

        foreach ($units as $index => $unit) {
            $rawValue = ($unit['base_price'] * $targetSum) / $baseUnitsSum;
            $floorValue = (int) floor($rawValue);

            $allocated[$index] = $floorValue;
            $remainders[$index] = $rawValue - $floorValue;
            $allocatedSum += $floorValue;
        }

        $leftToDistribute = max($targetSum - $allocatedSum, 0);
        arsort($remainders);

        foreach (array_keys($remainders) as $index) {
            if ($leftToDistribute <= 0) {
                break;
            }

            $allocated[$index] += 1;
            $leftToDistribute--;
        }

        $normalized = [];
        foreach ($units as $index => $unit) {
            $normalizedItem = $unit['item'];
            $normalizedItem['quentity'] = 1;
            $normalizedItem['price'] = $allocated[$index];
            $normalized[] = $normalizedItem;
        }

        return $normalized;
    }
}
