<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_offers_raw', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index()->comment('Наименование из XML');
            $table->string('size')->nullable()->index()->comment('Выделенный размер/модификатор');
            $table->string('barcode', 20)->unique()->comment('Штрихкод');
            $table->string('ext_id')->nullable()->index()->comment('Ид из 1С');
            $table->string('category_id')->nullable()->index()->comment('UUID категории 1С');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_offers_raw');
    }
};
