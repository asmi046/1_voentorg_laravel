<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use DB;

class VedomstvoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("vedomstvos")->insert(
            [
                [
                    "title" => "ВВ МВД",
                    "title_mini" => "ВВ МВД",
                    "slug" => Str::slug("ВВ МВД"),
                    "description" => "Экипировка и принадлежности ВВ МВД",
                    "img" => "",
                    "seo_title" => "Экипировка и принадлежности ВВ МВД",
                    "seo_description" => "Экипировка и принадлежности ВВ МВД"
                ],

                [
                    "title" => "ВВС",
                    "title_mini" => "ВВС",
                    "slug" => Str::slug("ВВС"),
                    "description" => "Экипировка и принадлежности ВВС",
                    "img" => "",
                    "seo_title" => "Экипировка и принадлежности ВВС",
                    "seo_description" => "Экипировка и принадлежности ВВС"
                ],
            ]
        );
    }
}
