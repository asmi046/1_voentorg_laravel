<?php

namespace App\Listeners;

use App\Events\BascetOrderCreated;
use App\Mail\Cart\BascetSend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBascetMailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BascetOrderCreated $event): void
    {
        Mail::to(config('cart.send_to'))->send(new BascetSend($event->formData));
    }
}
