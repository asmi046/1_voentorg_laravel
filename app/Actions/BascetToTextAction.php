<?php

namespace App\Actions;

use App\Models\ShopOrder;

class BascetToTextAction
{
    public function handle(array $formData, int $zakaz_id)
    {
        $rez_text = "<b>Оформлен заказ</b>\n\r";
        $rez_text .= '<b>№ '.$zakaz_id."</b>\n\r";

        $rez_text .= '<strong>Имя:</strong> '.($formData['fio'] ?? '')."\n\r";
        $rez_text .= '<strong>Телефон:</strong> '.($formData['phone'] ?? '')."\n\r";
        $rez_text .= '<strong>E-mail:</strong> '.($formData['email'] ?? '')."\n\r";

        if (! empty($formData['delivery_text'])) {
            $rez_text .= "\n\r<strong>Доставка:</strong>\n\r";
            $rez_text .= str_replace("\n", "\n\r", $formData['delivery_text'])."\n\r";
        }

        if (! empty($formData['promo_code'])) {
            $rez_text .= '<strong>Промокод:</strong> '.($formData['promo_code'] ?? '')."\n\r";
            $rez_text .= '<strong>Скидка по промокоду:</strong> '.($formData['promo_code_discount'] ?? '')."\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach (($formData['tovars'] ?? []) as $item) {
            $rez_text .= ($item['tovar_content']['title'] ?? '').' (Артикул:'.($item['product_sku'] ?? '').')'."\n\r";
            $rez_text .= $item['price']." ₽\n\r";
            $rez_text .= 'Кол-во: '.$item['quentity']."\n\r";
            $rez_text .= 'Подитог: '.(float) $item['quentity'] * (float) $item['price']."\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Итого</b> ".($formData['count'] ?? 0).' товар(ов) на сумму '.($formData['amount'] ?? 0);

        return $rez_text;
    }

    public function handleShopOrder(ShopOrder $order): string
    {
        $rez_text = "<b>Оформлен заказ</b>\n\r";
        $rez_text .= '<b>№ '.$order->id."</b>\n\r";

        $rez_text .= '<strong>Имя:</strong> '.($order->name ?? '—')."\n\r";
        $rez_text .= '<strong>Телефон:</strong> '.($order->phone ?? '—')."\n\r";
        $rez_text .= '<strong>E-mail:</strong> '.($order->email ?? '—')."\n\r";

        if ($order->comment) {
            $rez_text .= '<strong>Комментарий:</strong> '.$order->comment."\n\r";
        }

        if ($order->promo_code) {
            $rez_text .= '<strong>Промокод:</strong> '.$order->promo_code."\n\r";
        }

        if ($order->delivery) {
            $rez_text .= "\n\r<strong>Доставка:</strong>\n\r";
            $rez_text .= '<strong>Способ:</strong> '.($order->delivery->method ?? '—')."\n\r";
            $rez_text .= '<strong>Провайдер:</strong> '.($order->delivery->provider ?? '—')."\n\r";
            $rez_text .= '<strong>Город:</strong> '.($order->delivery->city ?? '—')."\n\r";

            if ($order->delivery->delivery_address) {
                $rez_text .= '<strong>Адрес:</strong> '.$order->delivery->delivery_address;
                if ($order->delivery->apartment) {
                    $rez_text .= ', кв. '.$order->delivery->apartment;
                }
                $rez_text .= "\n\r";
            }

            if ($order->delivery->pickup_point_address) {
                $rez_text .= '<strong>ПВЗ:</strong> '.$order->delivery->pickup_point_address."\n\r";
            }

            if ($order->delivery->price) {
                $rez_text .= '<strong>Цена доставки:</strong> '.number_format((float) $order->delivery->price, 2, '.', '')." ₽\n\r";
            }

            if ($order->delivery->delivery_date_range) {
                $start = $order->delivery->delivery_date_range['start'] ?? '';
                $end = $order->delivery->delivery_date_range['end'] ?? '';
                if ($start || $end) {
                    $rez_text .= '<strong>Дата доставки:</strong> '.$start;
                    if ($end) {
                        $rez_text .= ' — '.$end;
                    }
                    $rez_text .= "\n\r";
                }
            }
        }

        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach ($order->items as $item) {
            $rez_text .= ($item->product_name ?? $item->product_title ?? 'Товар').' (Артикул: '.$item->product_sku.")\n\r";
            $rez_text .= number_format((float) $item->price, 2, '.', '')." ₽\n\r";
            $rez_text .= 'Кол-во: '.$item->quantity."\n\r";
            $rez_text .= 'Подитог: '.number_format((float) ($item->price * $item->quantity), 2, '.', '')." ₽\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Итого</b> ".$order->items->sum('quantity').' товар(ов) на сумму '.number_format((float) $order->total_summ, 2, '.', '').' ₽';

        return $rez_text;
    }
}
