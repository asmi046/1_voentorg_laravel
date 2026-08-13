<?php

return [

    /*
    |--------------------------------------------------------------------------
    | СДЭК
    |--------------------------------------------------------------------------
    |
    | Параметры подключения к API СДЭК.
    | Базовый адрес среды, логин и пароль (client_id / client_secret).
    |
    */

    'base_url' => env('CDEK_BASE_URL', 'https://api.edu.cdek.ru/v2'),

    'login' => env('CDEK_LOGIN'),

    'password' => env('CDEK_PASSWORD'),

    'shipment_point' => env('CDEK_SHIPMENT_POINT'),

    'sender_city_code' => env('CDEK_SENDER_CITY_CODE', '699'),

    /*
    |--------------------------------------------------------------------------
    | Данные отправителя
    |--------------------------------------------------------------------------
    |
    | Информация о магазине-отправителе для заказа СДЭК.
    |
    */

    'sender' => [
        'company' => env('CDEK_SENDER_COMPANY', config('app.name')),
        'name' => env('CDEK_SENDER_NAME', 'Магазин'),
        'email' => env('CDEK_SENDER_EMAIL', config('mail.from.address')),
        'phone' => env('CDEK_SENDER_PHONE', '79000000000'),
    ],

];
