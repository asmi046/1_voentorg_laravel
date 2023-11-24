<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Storage;
use DB;

use Illuminate\Support\Facades\App;


class CategorySeederArray extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        include "categories.php";

        foreach ($categories as $item) {
            DB::table("categories")->insert($item);
        }
    }
}
