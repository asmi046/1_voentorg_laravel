<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->id()->comment('Идентификатор корзины');
            $table->timestamps();

            // Идентификация корзины
            $table->string('session_id', 255)->comment('Идентификатор сессии пользователя');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Идентификатор авторизованного пользователя');

            // Уникальность: либо сессия без пользователя, либо пользователь без сессии
            $table->unique(['session_id', 'user_id'], 'shop_carts_unique_session_user');

            // Индексы
            $table->index('session_id', 'shop_carts_session_id_index');
            $table->index('user_id', 'shop_carts_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_carts');
    }
};
