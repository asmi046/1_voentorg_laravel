<?php

namespace App\Listeners;

use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;
use App\Events\BascetOrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBascetToTelegramListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BascetOrderCreated $event): void
    {
        $toText = new BascetToTextAction();
        $tgSender = new TelegramSendAction();

        $text = $toText->handle($event->formData, $event->orderId);
        $tgSender->handle($text);
    }
}
