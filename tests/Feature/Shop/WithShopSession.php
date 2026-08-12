<?php

namespace Tests\Feature\Shop;

use App\Http\Middleware\EncryptCookies;
use Illuminate\Support\Str;

/**
 * Обеспечивает персистентную сессию между HTTP-запросами в feature-тестах.
 *
 * По умолчанию Laravel не переносит session cookie между JSON-запросами в тестах,
 * что приводит к генерации нового session ID на каждый запрос. Этот трейт
 * исключает session cookie из шифрования и фиксирует стабильный session ID,
 * чтобы несколько запросов в одном тесте обращались к одной корзине.
 */
trait WithShopSession
{
    protected string $shopSessionId;

    protected function setUpShopSession(): void
    {
        $this->shopSessionId = Str::random(40);

        // Исключаем session cookie из шифрования/дешифрования,
        // иначе EncryptCookies middleware обнуляет значение.
        EncryptCookies::except([config('session.cookie')]);

        // Отключаем шифрование исходящих cookie и включаем передачу
        // cookie для JSON-запросов (по умолчанию отключена).
        $this->disableCookieEncryption();
        $this->withCredentials();
        $this->withUnencryptedCookie(config('session.cookie'), $this->shopSessionId);
    }
}
