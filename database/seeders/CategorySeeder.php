<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        // gwEJ s36l z7T3 sq5x qZZj dYzd

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
            DB::table("categories")->insert([
                "title" => $item["name"],
                "slug" => $item["slug"],
                "parent" => 0,
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
