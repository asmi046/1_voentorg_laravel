<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Storage;

use DB;

class ProductSeeder extends Seeder
{

    protected $categories;
    protected $image_load = false;

    protected function string_clear($str) {
        return str_replace(['&#171;','&#187;'], "", $str);
    }


    protected function get_tovar_item($item, $index) {
        $tmp = [];

        $sku = (!empty($item["meta"]["_sku_n"]))?$item["meta"]["_sku_n"]:"tov_".$item["id"];
        if ($sku === " ") $sku = "tov_".$item["id"];

        try{
            $thumb = $this->get_media_data($item["featured_media"]);
            $img = "";
            if (!empty($thumb))
                {
                    if ($this->image_load)
                        Storage::disk('public')->put($thumb["file_name"], file_get_contents($thumb["url"]), 'public');
                    $img = Storage::url($thumb["file_name"]);
                }

        } catch (\Throwable $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $img = "";
        }

        $tmp["group"] = "";
        $tmp["sku"] = $sku;
        $tmp["title"] = $this->string_clear( $item["title"]["rendered"] );
        $tmp["slug"] = $item["slug"];
        $tmp["img"] = $img;
        $tmp["description"] = $item["meta"]["_un_derscr"];
        $tmp["short_description"] = $item["meta"]["_un_short_derscr"];
        $tmp["specification"] = $item["meta"]["_un_specifications"];
        $tmp["viev_count"] = rand(1, 20);

        $tmp["old_site_id"] = $item["id"];

        $tmp["hit"] = rand(0, 1);
        $tmp["new"] = rand(0, 1);

        $tmp["seo_title"] = $this->string_clear( $item["title"]["rendered"] );
        $tmp["seo_description"] = str_replace("#post_excerpt ","",$item["meta"]["_aioseo_description"]);


        return $tmp;
    }

    protected function set_category($item, $t_id) {
        $result = [];
        foreach ($item["categories"] as $cat)
        {
            if (isset($this->categories[$cat]))
                $result[] = [
                    "product_id" => $t_id,
                    "category_id" => $this->categories[$cat]
                ];
        }

        return $result;
    }

    protected function get_media_data($id) {

        if (empty($id)) return [];




            $data = \WordPress::media()
            ->find(1, ['id' => $id]);


            if (!isset($data['guid'])) return [];

            $file_info = pathinfo($data['guid']['rendered']);

            return [
                'url' => $data['guid']['rendered'],
                'alt' => $data['alt_text'],
                'file_name' => $data['slug'].".".$file_info['extension'],
            ];
    }

    protected function set_galery($item, $t_id) {

        for($i = 1; $i<=4; $i++)
        {
            $thumb = $this->get_media_data($item["meta"]["_un_gallery_img_".$i]);

            if (!empty($thumb))
            {
                try {
                    if ($this->image_load)
                        Storage::disk('public')->put($thumb["file_name"], file_get_contents($thumb["url"]), 'public');

                    DB::table("product_images")->insert([
                        "product_id" => $t_id,
                        "link" => Storage::url($thumb["file_name"]),
                        "alt" => $thumb["alt"],
                        "title" => $thumb["alt"],
                    ]);

                } catch (\Throwable $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
        }


    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->categories = (array)json_decode(file_get_contents(public_path("tmp//categories.json")));

        if (empty($this->categories)) {
            print_r("Категории не заданы");
            return;
        }

        $page = 0;
        $index = 0;


            do
            {


                $page++;

                $all_posts = \WordPress::posts()
                ->perPage(100)
                ->page($page)
                ->get();


                foreach ($all_posts["data"] as $item) {
                    // if ($index < 317) {
                    //     $index++;
                    //     continue;
                    // }

                    $tovar = $this->get_tovar_item($item, $index);

                    print_r($index.": ".$tovar["old_site_id"]." -> ".$tovar["title"]." -> ".$tovar["sku"]."\n\r" );

                    try {
                        DB::table("products")->insert($tovar);
                    } catch (UniqueConstraintViolationException $e) {
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        continue;
                    }


                    $t_id = DB::getPdo()->lastInsertId();

                    $cats = $this->set_category($item, $t_id);
                    DB::table("category_product")->insert($cats);

                    $this->set_galery($item, $t_id);

                    DB::table("product_prices")->insert([
                        'product_id' => $t_id,
                        'sku' => $tovar["sku"],
                        'value' => "",
                        'price' => floatval(str_replace(" ","",$item["meta"]["_price_n"])),
                        'old_price' => floatval(str_replace(" ","",$item["meta"]["_price_old"])),
                    ]);


                    $index++;

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
