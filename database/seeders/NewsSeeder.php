<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->put("news_brn.webp", file_get_contents(public_path('tmp_content/news_brn.webp')), 'public');

        DB::table("news")->insert(
            [
                [
                    'created_at' => now(),
                    'title' => 'Новое поступление бронижелетов',
                    'slug' => Str::slug("Новое поступление бронижелетов"),
                    'img' => Storage::url("news_brn.webp"),
                    'short_description' => "Новое поступление бронижелетов",
                    'description' => file_get_contents(public_path('tmp_content//npbrg.html')),
                    'seo_title' => 'Новое поступление бронижелетов',
                    'seo_description' => 'Новое поступление бронижелетов',
                ],

                [
                    'created_at' => now(),
                    'title' => 'Новое поступление бронижелетов 1',
                    'slug' => Str::slug("Новое поступление бронижелетов 1"),
                    'img' => Storage::url("news_brn.webp"),
                    'short_description' => "Новое поступление бронижелетов 1",
                    'description' => file_get_contents(public_path('tmp_content//npbrg.html')),
                    'seo_title' => 'Новое поступление бронижелетов 1',
                    'seo_description' => 'Новое поступление бронижелетов 1',
                ],




        ]);
    }
}
