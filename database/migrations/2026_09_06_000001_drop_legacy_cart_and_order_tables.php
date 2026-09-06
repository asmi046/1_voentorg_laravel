<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Полный переход на новую логику корзины/заказов (shop_carts / shop_orders).
 *
 * Удаляет legacy-таблицы: carts, orders, order_product, order_products.
 * Миграция безопасна для повторного запуска (down() восстанавливает исходные таблицы).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Дочерние таблицы ссылаются на orders.id через FK — отключаем проверку,
        // иначе СУБД запретит дроп parent-таблицы. Laravel делает это
        // кросс-платформенно (MySQL / PostgreSQL / SQLite).
        Schema::disableForeignKeyConstraints();

        try {
            foreach (['order_product', 'order_products', 'carts', 'orders'] as $table) {
                if (Schema::hasTable($table)) {
                    Schema::drop($table);
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone');
                $table->string('adress')->nullable();
                $table->string('comment')->nullable();
                $table->string('session_id');
                $table->integer('user_id');
                $table->string('pay_order')->nullable();
                $table->integer('pay_status')->nullable();
                $table->string('pay_status_text')->nullable();
            });
        }

        if (! Schema::hasTable('order_product')) {
            Schema::create('order_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('order_products')) {
            Schema::create('order_products', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->string('product_sku', 150);
                $table->integer('quentity');
                $table->double('price', 12, 2)->default(0);
            });
        }

        if (! Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('session_id');
                $table->integer('user_id');
                $table->string('product_sku', 150);
                $table->integer('quentity');
                $table->double('price', 12, 2)->default(0);
                $table->foreignId('product_id');
            });
        }
    }
};
