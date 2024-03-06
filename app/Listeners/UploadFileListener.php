<?php

namespace App\Listeners;

use App\Actions\WaterMark;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Orchid\Platform\Events\UploadFileEvent;

class UploadFileListener
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UploadFileEvent $event): void
    {
        Log::info("Накладываем марку");

        $img_patch = Storage::path($event->attachment->disk."/".$event->attachment->path.$event->attachment->name.".".$event->attachment->extension);
        $watermark = new WaterMark();
        $img = imagecreatefromjpeg($img_patch);
        $water = imagecreatefrompng(public_path('watermark/wm.png'));
        $im=$watermark->handle($img,$water,10);
        imagejpeg($im, $img_patch);
        Log::info("Закончили");
    }
}
