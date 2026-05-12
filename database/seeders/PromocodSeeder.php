<?php

namespace Database\Seeders;

use App\Models\Promocod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromocodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promocods')->truncate();

        Promocod::create([
            'promo_type' => 'Регулярный',
            'promo_code' => 'OZON',
            'valid_from' => null,
            'valid_to' => null,
            'discount_percent' => 10,
            'fixed_discount_amount' => 0,
            'minimum_cart_amount' => 0,
        ]);
    }
}