<?php

namespace App\Mail\Cart;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderSend extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ShopOrder $order,
    ) {}

    public function build()
    {
        return $this->from(config('cart.send_from'), config('cart.send_from_text'))
            ->subject('Новый заказ №'.$this->order->id.' на сайте')
            ->replyTo(config('cart.reply_to'), config('cart.reply_to_text'))
            ->view('mail.shopordermail')
            ->with([
                'order' => $this->order,
            ]);
    }
}
