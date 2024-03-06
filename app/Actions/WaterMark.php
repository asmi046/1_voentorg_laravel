<?php

namespace App\Actions;


class WaterMark {
    public function handle( $main_img_obj, $watermark_img_obj, $alpha_level = 100) {
        $watermark_width = imagesx($watermark_img_obj);
        $watermark_height = imagesy($watermark_img_obj);

        $dest_x = imagesx($main_img_obj) - $watermark_width - 5;
        $dest_y = imagesy($main_img_obj) - $watermark_height - 5;
        imagecopymerge($main_img_obj, $watermark_img_obj, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $alpha_level);

        return $main_img_obj;
    }
}
