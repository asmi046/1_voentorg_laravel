<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("title")->comment("Название");
            $table->string("slug")->comment("Слаг");
            $table->integer("parent")->nullable()->comment("Родительская категория (ID)");
            $table->text("description")->nullable()->comment("Описание");
            $table->string("img")->nullable()->comment("Изображение");
            $table->string('seo_title')->nullable()->comment("seo заголовок");
            $table->text('seo_description')->nullable()->comment("seo описание");
        });


        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('categories');
    }
};
