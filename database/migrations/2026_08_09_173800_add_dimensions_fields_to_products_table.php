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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('weight')->nullable()->after('order')->comment('Вес товара (граммы)');
            $table->integer('length')->nullable()->after('weight')->comment('Длина товара (см)');
            $table->integer('width')->nullable()->after('length')->comment('Ширина товара (см)');
            $table->integer('height')->nullable()->after('width')->comment('Высота товара (см)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'length', 'width', 'height']);
        });
    }
};
