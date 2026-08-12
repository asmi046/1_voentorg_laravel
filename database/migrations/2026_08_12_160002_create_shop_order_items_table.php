<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id()->comment('Идентификатор позиции заказа');
            $table->timestamps();

            // Связь с заказом
            $table->unsignedBigInteger('order_id')->comment('Идентификатор заказа (shop_orders.id)');

            // Снапшот данных товара (фиксируется на момент оформления заказа).
            // Связь с актуальным товаром намеренно не хранится: товары могут
            // сниматься с продажи и удаляться, поэтому позиция заказа опирается
            // исключительно на снапшоты (SKU, название, цена), а не на FK.
            $table->string('product_sku', 150)->comment('Артикул товара (SKU) на момент заказа');
            $table->string('product_name', 255)->nullable()->comment('Название товара (снапшот на момент заказа)');
            $table->string('product_title', 500)->nullable()->comment('Полное название товара (снапшот на момент заказа)');

            // Цена и количество
            $table->decimal('price', 12, 2)->comment('Цена за единицу товара на момент заказа');
            $table->integer('quantity')->default(1)->comment('Количество единиц товара');

            // Дополнительно
            $table->integer('weight_grams')->nullable()->comment('Вес единицы товара в граммах');
            $table->json('dimensions')->nullable()->comment('Габариты единицы товара {"length":55,"width":30,"height":25}');

            // Сырые данные
            $table->json('raw_data')->nullable()->comment('Дополнительные данные товара на момент заказа');

            // Внешний ключ на заказ
            $table->foreign('order_id')
                ->references('id')
                ->on('shop_orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // Индексы
            $table->index('product_sku', 'shop_order_items_product_sku_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
};
