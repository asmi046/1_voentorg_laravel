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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->nullable()->comment('Название магазина');
            $table->string('adress')->comment('Адрес магазина');
            $table->string('geo')->comment('Координаты на карте магазина');
            $table->string('orientir')->nullable()->comment('Ориентир на местности');
            $table->string('phone')->nullable()->comment('Телефон магазина');
            $table->string('email')->nullable()->comment('E-mail магазина');
            $table->string('time_work')->comment('Время работы магазина');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
