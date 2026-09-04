<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_offers_data', function (Blueprint $table) {
            $table->id();
            $table->string('ext_id')->unique()->comment('Ид из 1С (Предложение/Ид)');
            $table->string('name')->nullable()->comment('Наименование из 1С');
            $table->double('price', 10, 2)->default(0)->comment('ЦенаЗаЕдиницу');
            $table->integer('count')->default(0)->comment('Количество');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_offers_data');
    }
};
