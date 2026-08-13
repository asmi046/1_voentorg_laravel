<?php

namespace App\Listeners;

use App\Events\ShopOrderCreated;
use App\Mail\Cart\ShopOrderSend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendShopOrderMailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ShopOrderCreated $event): void
    {
        Mail::to(config('cart.send_to'))->send(new ShopOrderSend($event->order));
    }
}
