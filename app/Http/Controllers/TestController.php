<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class TestController extends Controller
{
    public function index() {
        $manager = new ImageManager(new Driver());
        $img = $manager->read(public_path('watermark/8m.jpg'));
        $img_wm = $manager->read(public_path('watermark/wm_s2.png'));

        $wm_pos_right = ($img->width() - $img_wm->width())/2;
        $wm_pos_bottom = ($img->height() - $img_wm->height())/2;

        $img->place(
            $img_wm,
            'bottom-right',
            $wm_pos_right,
            $wm_pos_bottom,
            100
        );
        $img->save(public_path('watermark/rez.jpg'));
    }
}
