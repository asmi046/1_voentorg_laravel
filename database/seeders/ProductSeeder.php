<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    protected function string_clear($str) {
        return str_replace(['&#171;','&#187;'], "", $str);
    }


    protected function get_tovar_item($item) {

    }

    protected function get_media_data($id) {
        $data = \WordPress::media()
        ->find(1, ['id' => $id]);

        return [
            'url' => $data['guid']['rendered'],
            'alt' => $data['alt_text'],
        ];
    }


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = json_decode(file_get_contents(public_path("tmp//categories.json")));
        if (empty($categories)) {
            print_r("Категории не заданы");
            return;
        }

        $page = 0;

        do
        {
            $page++;

            $all_posts = \WordPress::posts()
            ->perPage(100)
            ->page($page)
            ->get();

            foreach ($all_posts["data"] as $item) {
                print($this->string_clear( $item["title"]["rendered"] )."\n\r");
                print_r($this->get_media_data($item["featured_media"]));
            }

        }
        while ($page <= 16);


    // 'price_n'
    // 'price_old'
    // 'sku_n'
    // 'stiker'
    // 'nal'

    // 'un_specifications'
    // 'un_short_derscr'
    // 'un_derscr'
    // 'un_application'

    // 'un_gallery_img_1'
    // 'un_gallery_img_2'
    // 'un_gallery_img_3'
    // 'un_gallery_img_4'

    }
}
