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

        $ved = [
            "ВВС, ВКС" => "ввс_вкс.webp",
            "ВДВ" => "вдв.webp",
            "ВМФ" => "ВМФ.webp",
            "Юнармия" => "Юнармия.webp",
            "ВС РФ, Вооруженные силы" => "ВС_РФ_вооруженные_силы.webp",
            "ГИБДД, ДПС" => "гибдд_ДПС.webp",
            "Казачество" => "Казачество.webp",
            "МВД" => "мвд.webp",
            "МЧС" => "мчс.webp",
            "ОМОН" => "Омон.webp",
            "Охрана" => "Охрана.webp",
            "Пограничная Служба" => "Пограничная_Служба.webp",
            "Полиция" => "Полиция.webp",
            "ППС" => "ППС.webp",
            "Росгвардия" => "Росгвардия.webp",
            "Следственный Комитет" => "Следственный_Комитет.webp",
            "СОБР" => "Собр.webp",
            "ФСБ" => "ФСБ.webp",
            "ФСИН" => "ФСИН.webp",
            "ФСО" => "ФСО.webp",
            "Юстиция" => "Юстиция.webp",
        ];

        foreach ($ved as $key => $photo)
        {
            Storage::disk('public')->put($photo, file_get_contents(public_path('tmp\\vedomstva\\'.$photo)), 'public');
            DB::table("vedomstvos")->insert(
                [
                    [
                        "title" => $key,
                        "title_mini" => $key,
                        "slug" => Str::slug($key),
                        "description" => "Экипировка и принадлежности ".$key,
                        'img' => Storage::url($photo),
                        "seo_title" => "Экипировка и принадлежности ".$key,
                        "seo_description" => "Экипировка и принадлежности ".$key
                    ],
                ]
            );
        }
    }
}
