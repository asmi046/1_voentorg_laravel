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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('post_index', 20)->index();
            $table->string('type', 50)->nullable();
            $table->string('name');
            $table->foreignId('region_id')->nullable();
            $table->foreignId('district_id')->nullable();
            $table->string('settlement_type', 50)->nullable();
            $table->string('settlement', 100)->nullable();
            $table->string('kladr_code', 20)->nullable();
            $table->uuid('fias_code')->nullable()->unique();
            $table->string('fias_level', 50)->nullable();
            $table->integer('center_sign')->nullable();
            $table->string('okato_code', 20)->nullable();
            $table->string('oktmo_code', 20)->nullable();
            $table->string('tax_code', 10)->nullable();
            $table->string('timezone', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('federal_district', 50)->nullable();
            $table->integer('population')->nullable();
            $table->timestamps();

            $table->index(['name', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
