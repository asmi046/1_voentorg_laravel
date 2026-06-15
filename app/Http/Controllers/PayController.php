<?php

namespace App\Http\Controllers;

use App\Services\YooKassaService;

class PayController extends Controller
{
    public function pay_hook(YooKassaService $pay) {
        $pay->pay_fixation();
    }

}
