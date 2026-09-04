<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ved = [
            'Тактическое снаряжение и экипировка' => 'tactical-snaryag.webp',
            'Уставная форма Юнармии' => 'forma-yunarmii.webp',
            'Форма Великой Отечественной' => 'forma-vov-velikoj-otechestvennoj-vojny.webp',
        ];

        foreach ($ved as $key => $photo) {
            Storage::disk('public')->put($photo, file_get_contents(public_path('tmp/baner/'.$photo)), 'public');
            DB::table('banners')->insert(
                [
                    [
                        'title' => $key,
                        'img' => Storage::url($photo),
                    ],
                ]
            );
        }
    }
}
