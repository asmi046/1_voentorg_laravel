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
        Schema::create('promocods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('promo_type')->comment('Тип промокода');
            $table->string('promo_code')->comment('Промокод');
            $table->date('valid_from')->nullable()->comment('Действует с');
            $table->date('valid_to')->nullable()->comment('Действует по');
            $table->integer('discount_percent')->default(0)->comment('Процент скидки');
            $table->integer('fixed_discount_amount')->default(0)->comment('Фиксированная сумма скидки');
            $table->integer('minimum_cart_amount')->default(0)->comment('Сумма с которой действует промокод');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocods');
    }
};