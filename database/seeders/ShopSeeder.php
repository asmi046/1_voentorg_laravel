<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("shops")->insert(
            [
                [
                    'name' => '1-й ВОЕНТОРГ',
                    'adress' => 'г. Курск, ул. Верхняя Луговая, д. 6 (1 этаж)',
                    'geo' => '51.72815122248612,36.1831243822784',
                    'orientir' => 'напротив стоянки центрального рынка',
                    'phone' => '',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => '9:00–19:00'
                ],
            ]
        );

    }
}
