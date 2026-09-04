<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->string('ext_id')->nullable()->after('sku')->index()->comment('Ид из 1С');
            $table->string('category_id')->nullable()->after('ext_id')->index()->comment('UUID категории 1С');
            $table->integer('count')->nullable()->after('value')->default(0)->comment('Количество на складе');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn(['ext_id', 'category_id', 'count']);
        });
    }
};
