<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;

use Illuminate\Support\Facades\Mail;
use App\Mail\BascetSend;
use App\Http\Requests\BascetForm;

class CartController extends Controller
{
    public function index() {
        return view('bascet');
    }

    public function add(Request $request) {
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

    public function send(BascetForm $request) {
        $order = Order::create([
            'name' => $request->input('fio'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'adress' => $request->input('adress'),
            'comment' => $request->input('comment'),
            'position_count' => $request->input('count'),
            'amount' => $request->input('amount'),
            'delivery' => $request->input('delivery'),
            'pay' => $request->input('pay'),
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,

        ]);

        // foreach ($request->input('tovars') as $item) {
        //     $order->orderCart()->create($item);
        // }

        // $order->orderProducts()->sync(array_column($request->input('tovars'), "id"));

        Mail::to(["asmi046@gmail.com","cubensis2009@yandex.ru"])->send(new BascetSend($request));

        return ['send' => true];
    }

    public function thencs() {
        Cart::cart_clear();
        return view("thencs");
    }
}
