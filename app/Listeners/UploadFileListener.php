<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
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
        Log::info('Накладываем марку');

        $manager = new ImageManager(new Driver);

        $img_patch = Storage::path($event->attachment->disk.'/'.$event->attachment->path.$event->attachment->name.'.'.$event->attachment->extension);
        $wm_patch = public_path('watermark/wm_s2.png');

        $img = $manager->read($img_patch);
        $img_wm = $manager->read(public_path('watermark/1000_750_3.png'));

        $wm_pos_right = ($img->width() - $img_wm->width()) / 2;
        $wm_pos_bottom = ($img->height() - $img_wm->height()) / 2;

        $img->place(
            $img_wm,
            'center',
            0,
            0,
            100
        );

        $img->save($img_patch);

        Log::info('Закончили');
    }
}
