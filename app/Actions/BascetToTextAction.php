<?php

namespace App\Actions;

class BascetToTextAction {
    public function handle(array $formData, int $zakaz_id) {
        $rez_text = "<b>Оформлен заказ</b>\n\r";
        $rez_text .= "<b>№ ".$zakaz_id."</b>\n\r";

        $rez_text .= "<strong>Имя:</strong> ".($formData['fio'] ?? '')."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".($formData['phone'] ?? '')."\n\r";
        $rez_text .= "<strong>E-mail:</strong> ".($formData['email'] ?? '')."\n\r";

        if (!empty($formData['promo_code'])) {
            $rez_text .= "<strong>Промокод:</strong> ".($formData['promo_code'] ?? '')."\n\r";
            $rez_text .= "<strong>Скидка по промокоду:</strong> ".($formData['promo_code_discount'] ?? '')."\n\r";
        }



        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach (($formData['tovars'] ?? []) as $item) {
            $rez_text .= ($item["tovar_content"]["title"] ?? '')." (Артикул:".($item["product_sku"] ?? '').")"."\n\r";
            $rez_text .= $item["price"]." ₽\n\r";
            $rez_text .= "Кол-во: " . $item["quentity"]."\n\r";
            $rez_text .= "Подитог: " . (float)$item["quentity"] * (float)$item["price"]."\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Итого</b> " . ($formData['count'] ?? 0) . " товар(ов) на сумму ".($formData['amount'] ?? 0);

        return $rez_text;
    }
}
