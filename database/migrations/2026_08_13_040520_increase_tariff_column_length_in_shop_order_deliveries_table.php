<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shop_order_deliveries', function (Blueprint $table) {
            $table->string('tariff', 500)->nullable()->change()->comment('Применённый тариф доставки');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_order_deliveries', function (Blueprint $table) {
            $table->string('tariff', 100)->nullable()->change()->comment('Применённый тариф доставки');
        });
    }
};
