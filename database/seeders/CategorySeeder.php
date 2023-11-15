<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Storage;
use DB;

use Illuminate\Support\Facades\App;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

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


    public function run(): void
    {
        // gwEJ s36l z7T3 sq5x qZZj dYzd

        $RR = \Http::get('https://1voentorg.ru/wp-json/wp/v2/categories?per_page=100&parent=247');

        print_r($RR);

        $category = [];

        $cat_sootn = [];

        $all_cat = \WordPress::categories()
        // ->fields(["id", "name", "slug", "parent", "description"])
        ->perPage(100)
        ->parameter("parent", 247)
        ->get();




        foreach ($all_cat["data"] as $item) {

            $subcat = \WordPress::categories()
                ->fields(["id", "name", "slug", "parent", "description"])
                ->perPage(100)
                ->parameter("parent", $item['id'])
                ->get();

            $item["subcat"] = $subcat["data"];

            $category[] = $item;

        }

        foreach ($category as $item) {
            $parentId = $item["id"];

            try{
                $thumb = $this->get_media_data($item["meta"]["_cat_preview"]);
                $img = "";
                if (!empty($thumb))
                    {
                        Storage::disk('public')->put($thumb["file_name"], file_get_contents($thumb["url"]), 'public');
                        $img = Storage::url($thumb["file_name"]);
                    }

            } catch (\Throwable $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                $img = "";
            }

            DB::table("categories")->insert([
                "title" => $item["name"],
                "slug" => $item["slug"],
                "parent" => 0,
                "img" => $img,
                "description" => $item["description"],
                "seo_title" =>  $item["name"]. " купить в Курске",
                "seo_description" =>  $item["name"]. " купить в Курске по выгодной цене. Доставка по России.",
            ]);

            $cat_sootn[$parentId] = DB::getPdo()->lastInsertId();

            foreach ($item["subcat"] as $subitem) {
                DB::table("categories")->insert([
                    "title" => $subitem["name"],
                    "slug" => $subitem["slug"],
                    "parent" => $parentId,
                    "description" => $subitem["description"],
                    "seo_title" =>  $subitem["name"]. " купить в Курске",
                    "seo_description" =>  $subitem["name"]. " купить в Курске по выгодной цене. Доставка по России.",
                ]);


            $cat_sootn[$subitem["id"]] = DB::getPdo()->lastInsertId();
            }


        }

        print_r($cat_sootn);
        file_put_contents(public_path("tmp//categories.json"), json_encode($cat_sootn));
    }
}
