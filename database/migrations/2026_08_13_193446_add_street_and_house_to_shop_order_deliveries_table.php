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
            $table->string('street', 255)->nullable()->after('delivery_address')->comment('Улица доставки (для курьера)');
            $table->string('house', 20)->nullable()->after('street')->comment('Номер дома (для курьера)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_order_deliveries', function (Blueprint $table) {
            $table->dropColumn(['street', 'house']);
        });
    }
};
