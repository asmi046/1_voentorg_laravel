<?php

namespace App\Actions;

use App\Http\Requests\Cart\BascetForm;

class BascetToTextAction {
    public function handle(BascetForm $request, $zakaz_id) {
        $rez_text = "<b>Оформлен заказ</b>\n\r";
        $rez_text .= "<b>№ ".$zakaz_id."</b>\n\r";

        $rez_text .= "<strong>Имя:</strong> ".$request->input("fio")."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".$request->input("phone")."\n\r";
        $rez_text .= "<strong>E-mail:</strong> ".$request->input("email")."\n\r";


        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach ($request->input('tovars') as $item) {
            $rez_text .= $item["tovar_content"]["title"]." (Артикул:".$request->input("product_sku").")"."\n\r";
            $rez_text .= $item["price"]." ₽\n\r";
            $rez_text .= "Кол-во: " . $item["quentity"]."\n\r";
            $rez_text .= "Подитог: " . (float)$item["quentity"] * (float)$item["price"]."\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Итого</b> " . $request->input("count") . " товар(ов) на сумму ".$request->input("amount");

        return $rez_text;
    }
}
