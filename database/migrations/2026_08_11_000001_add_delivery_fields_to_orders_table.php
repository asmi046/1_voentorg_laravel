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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery')->nullable()->after('pay_status_text');
            $table->string('delivery_type')->nullable()->after('delivery');
            $table->decimal('delivery_price', 10, 2)->nullable()->after('delivery_type');
            $table->json('delivery_info')->nullable()->after('delivery_price');
            $table->string('delivery_date_range')->nullable()->after('delivery_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery', 'delivery_type', 'delivery_price', 'delivery_info', 'delivery_date_range']);
        });
    }
};
