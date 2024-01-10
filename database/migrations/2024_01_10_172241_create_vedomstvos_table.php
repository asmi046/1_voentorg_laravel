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
        Schema::create('vedomstvos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("title")->comment("Название");
            $table->string("title_mini")->nullable()->comment("Название");
            $table->string("slug")->comment("Слаг");
            $table->text("description")->nullable()->comment("Описание");
            $table->string("img")->nullable()->comment("Изображение");
            $table->string('seo_title')->nullable()->comment("seo заголовок");
            $table->text('seo_description')->nullable()->comment("seo описание");
        });

        Schema::create('product_vedomstvo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vedomstvo_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_vedomstvo');
        Schema::dropIfExists('vedomstvos');
    }
};
