<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id()->comment('Идентификатор позиции корзины');
            $table->timestamps();

            // Связь с корзиной
            $table->unsignedBigInteger('cart_id')->comment('Идентификатор корзины (shop_carts.id)');

            // Данные товара (снапшот на момент добавления в корзину)
            $table->string('product_sku', 150)->comment('Артикул товара (SKU)');
            $table->integer('quantity')->default(1)->comment('Количество единиц товара');
            $table->decimal('price_snapshot', 12, 2)->nullable()->comment('Снапшот цены на момент добавления');
            $table->json('raw_data')->nullable()->comment('Снапшот данных товара на момент добавления');

            // Внешний ключ
            $table->foreign('cart_id')
                ->references('id')
                ->on('shop_carts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // Индексы
            $table->index('cart_id', 'shop_cart_items_cart_id_index');
            $table->index('product_sku', 'shop_cart_items_product_sku_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
    }
};
