<?php

namespace App\Listeners;

use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;
use App\Events\ShopOrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendShopOrderToTelegramListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ShopOrderCreated $event): void
    {
        $toText = new BascetToTextAction();
        $tgSender = new TelegramSendAction();

        $text = $toText->handleShopOrder($event->order);
        $tgSender->handle($text);
    }
}
