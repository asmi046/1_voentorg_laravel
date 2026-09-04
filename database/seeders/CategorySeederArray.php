<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CategorySeederArray extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        include 'categories.php';

        foreach ($categories as $item) {
            DB::table('categories')->insert($item);
        }
    }
}
