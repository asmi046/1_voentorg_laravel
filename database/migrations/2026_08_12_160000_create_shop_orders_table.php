<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id()->comment('Идентификатор заказа');
            $table->timestamps();

            // Данные клиента
            $table->string('name')->comment('ФИО покупателя');
            $table->string('email')->nullable()->comment('Email покупателя');
            $table->string('phone')->comment('Телефон покупателя');
            $table->text('comment')->nullable()->comment('Комментарий к заказу');

            // Промокод
            $table->string('promo_code', 50)->nullable()->comment('Применённый промокод');

            // Суммы заказа
            $table->decimal('cart_summ', 12, 2)->nullable()->comment('Сумма корзины без учёта скидки');
            $table->decimal('discount_summ', 12, 2)->default(0.00)->comment('Размер скидки');
            $table->decimal('total_summ', 12, 2)->nullable()->comment('Итоговая цена заказа (с учётом доставки и скидки)');

            // Данные платежа
            $table->string('payment_id', 255)->nullable()->comment('Идентификатор платежа во внешней платёжной системе');
            $table->string('payment_status', 50)->nullable()->comment('Статус оплаты (код)');
            $table->string('payment_status_text', 255)->nullable()->comment('Текстовое описание статуса оплаты');

            // Служебные поля
            $table->string('session_id', 255)->comment('Идентификатор сессии покупателя');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Идентификатор авторизованного пользователя');

            // Индексы
            $table->index('created_at', 'shop_orders_created_at_index');
            $table->index('payment_status', 'shop_orders_payment_status_index');
            $table->index('user_id', 'shop_orders_user_id_index');
            $table->index('session_id', 'shop_orders_session_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
