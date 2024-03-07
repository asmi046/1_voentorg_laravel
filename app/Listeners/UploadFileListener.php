<?php

namespace App\Listeners;

use App\Actions\WaterMark;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Orchid\Platform\Events\UploadFileEvent;
use Intervention\Image\Drivers\Gd\Driver;


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

        $manager = new ImageManager(new Driver());

        $img_patch = Storage::path($event->attachment->disk."/".$event->attachment->path.$event->attachment->name.".".$event->attachment->extension);
        $watermark = new WaterMark();
        $img = imagecreatefromjpeg($img_patch);
        $water = imagecreatefrompng(public_path('watermark/wm_s1.png'));
        $im=$watermark->handle($img,$water,40);
        imagejpeg($im, $img_patch);
        Log::info("Закончили");
    }
}
