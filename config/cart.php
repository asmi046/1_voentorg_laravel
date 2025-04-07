<?php

return [
    'send_from' => env('MAIL_FROM_ADDRESS', 'asmi046@gmail.com'),
    'send_from_text' => 'Магазин Военторг46',
    'send_to' => ["asmi046@gmail.com", "1voentorg@bk.ru"],
    'subject' => 'Заказ через корзину на сайте',
    'reply_to' => '1voentorg@bk.ru',
    'reply_to_text' => 'Магазин Военторг46',
    'min_price' => 5000
];
