<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // gwEJ s36l z7T3 sq5x qZZj dYzd

        $all_cat = \WordPress::categories()
        ->fields(["name", "slug", "parent"])
        ->perPage(100)
        ->parameter("parent", 247)
        ->get();

        foreach ($all_cat as $item) {
            print_r($item);
        }


    }
}
