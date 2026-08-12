<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_order_deliveries', function (Blueprint $table) {
            $table->id()->comment('Идентификатор записи о доставке');
            $table->timestamps();

            $table->unsignedBigInteger('order_id')->comment('Идентификатор заказа (shop_orders.id)');

            // Основные данные доставки
            $table->string('provider', 50)->nullable()->comment('Служба доставки (cdek, boxberry, dpd и т.д.)');
            $table->string('method', 50)->nullable()->comment('Способ доставки (pickup, courier, pickup_point)');

            // Стоимость
            $table->decimal('price', 10, 2)->nullable()->comment('Стоимость доставки');

            // Детали доставки
            $table->string('tariff', 100)->nullable()->comment('Применённый тариф доставки');
            $table->json('delivery_date_range')->nullable()->comment('Сроки доставки {"min":"2026-08-15","max":"2026-08-17"}');

            // Местоположение
            $table->string('city', 255)->nullable()->comment('Город доставки');
            $table->string('pickup_point_id', 100)->nullable()->comment('Идентификатор пункта выдачи');
            $table->string('pickup_point_address', 500)->nullable()->comment('Адрес пункта выдачи');
            $table->string('delivery_address', 500)->nullable()->comment('Адрес доставки (для курьера)');
            $table->string('apartment', 20)->nullable()->comment('Квартира / офис');

            // Сырые данные
            $table->json('raw_data')->nullable()->comment('Полный ответ API провайдера доставки');

            // Внешний ключ и индексы
            $table->foreign('order_id')
                ->references('id')
                ->on('shop_orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index('provider', 'shop_order_deliveries_provider_index');
            $table->index('pickup_point_id', 'shop_order_deliveries_pickup_point_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_deliveries');
    }
};
