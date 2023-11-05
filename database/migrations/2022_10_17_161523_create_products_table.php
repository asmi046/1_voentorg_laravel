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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('sku')->unique()->comment('Артикул');
            $table->string('group')->comment('Группа для вывода размеров или цветов');
            $table->string('title')->comment('Название товара');;
            $table->string('slug')->comment('Слаг');
            $table->string('img')->nullable()->comment('Заглавное изображение');
            $table->text('description')->nullable()->comment('Описание');
            $table->text('short_description')->nullable()->comment('Краткое Описание');
            $table->text('specification')->nullable()->comment('Спецификация');

            $table->integer('viev_count')->nullable()->default(0);

            $table->integer('old_site_id')->nullable()->default(0);

            $table->boolean("hit")->nullable();
            $table->boolean("new")->nullable();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
