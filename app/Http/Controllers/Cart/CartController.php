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
            $resPay = $pay->registerOrder($order, $request->input('tovars'));

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
}
