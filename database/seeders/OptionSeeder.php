<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        php_uname();

        // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

        DB::table("options")->insert(
            [
                [
                    "name" => "obmen",
                    "type" => "rich",
                    'title' => 'Обмен возврат',
                    "value" => file_get_contents(public_path('texts//obmen.txt')),
                ],

                [
                    "name" => "proizvodstvo",
                    "type" => "rich",
                    'title' => 'Производство',
                    "value" => file_get_contents(public_path('texts//proizvodstvo.txt')),
                ],

                [
                    "name" => "opt",
                    "type" => "rich",
                    'title' => 'Оптовые поставки',
                    "value" => file_get_contents(public_path('texts//opt.txt')),
                ],

                [
                    "name" => "policy",
                    "type" => "rich",
                    'title' => 'Политика конфиденциальности',
                    "value" => file_get_contents(public_path('texts//policy.txt')),
                ],

                [
                    "name" => "organization",
                    "type" => "plan",
                    'title' => 'Организация',
                    "value" => "ИП Арепьев И. М.",
                ],

                [
                    "name" => "adress_ur",
                    "type" => "plan",
                    'title' => 'Адрес (Юридический)',
                    "value" => "Курская область, Курский район, д.Зорино, ул. Пески, д.13",
                ],

                [
                    "name" => "adress_fk",
                    "type" => "plan",
                    'title' => 'Адрес (Фактический)',
                    "value" => "г. Курск, пр. Победы, 14",
                ],

                [
                    "name" => "inn",
                    "type" => "plan",
                    'title' => 'ИНН',
                    "value" => "463225653229",
                ],

                [
                    "name" => "ogrnip",
                    "type" => "plan",
                    'title' => 'ОГРНИП',
                    "value" => "311463210100023",
                ],

                [
                    "name" => "phone",
                    "type" => "plan",
                    'title' => 'Телефон',
                    "value" => "+7 (920) 710 25 55",
                ],

                [
                    "name" => "email",
                    "type" => "plan",
                    'title' => 'e-mail',
                    "value" => "info@florida46.ru",
                ],

                [
                    "name" => "email_send",
                    "type" => "plan",
                    'title' => 'e-mail для отправки',
                    "value" => "info@florida46.ru, asmi046@gmail.com",
                ],

                [
                    "name" => "main_h1",
                    "type" => "plan",
                    'title' => 'Заголовок главной страницы',
                    "value" => "Доставка цветов в Курске на официальном сайте «Флорида»!",
                ],

                [
                    "name" => "telegram_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка Telegram',
                    "value" => "#",
                ],

                [
                    "name" => "ok_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка Одноклассники',
                    "value" => "https://ok.ru/profile/571716606301",
                ],

                [
                    "name" => "vk_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка Vk',
                    "value" => "https://vk.com/onevoentorg",
                ],
            ]);
    }
}
