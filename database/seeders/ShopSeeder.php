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
                    'phone' => '+7 (4712) 73-04-49',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => 'ежедневно, 9:00–19:00'
                ],

                [
                    'name' => '1-й Военторг (Карла Маркса)',
                    'adress' => 'г. Курск, ул. Карла Маркса, д. 66/3',
                    'geo' => '51.7601945485304,36.1862521239252',
                    'orientir' => 'слева от МегаГринна',
                    'phone' => '+7 (951) 083-99-56',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => 'ежедневно, 9:00–19:00'
                ],

                [
                    'name' => '1-й Военторг (пос. Жукова)',
                    'adress' => 'Курский район, пос. им. М.Жукова , квартал 5, д.18',
                    'geo' => '51.713656421040476,36.331542485458286',
                    'orientir' => '',
                    'phone' => '+7 (951) 081-85-05',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => 'ежедневно, 9:00–19:00'
                ],

                [
                    'name' => '1-й Военторг (с. Солоти)',
                    'adress' => 'г. Валуйки, с. Солоти, ул. Советская , 21',
                    'geo' => '50.27965736853596,38.03862739632977',
                    'orientir' => '',
                    'phone' => '+7 (904) 097-16-14',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => 'ежедневно, 9:00–19:00'
                ],

                [
                    'name' => '1-й Военторг (Валуйки)',
                    'adress' => 'Белгородская область, Валуйки, улица 9 Января, 2',
                    'geo' => '50.21324978765717,38.10017813558192',
                    'orientir' => '',
                    'phone' => '+7 (920) 578-27-08',
                    'email' => '1voentorg@bk.ru',
                    'time_work' => 'ежедневно, 9:00–19:00'
                ]

            ]
        );

    }
}
