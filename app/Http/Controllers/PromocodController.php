<?php

namespace App\Http\Controllers;

use App\Models\Promocod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromocodController extends Controller
{
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'promocode' => ['nullable', 'required_without:promo_code', 'string'],
            'promo_code' => ['nullable', 'required_without:promocode', 'string'],
            'cart_sum' => ['required', 'numeric', 'min:0'],
        ]);

        $promoCode = trim($validated['promocode'] ?? $validated['promo_code']);
        $cartSum = (float) $validated['cart_sum'];

        $promocod = Promocod::query()
            ->whereRaw('LOWER(promo_code) = ?', [mb_strtolower($promoCode, 'UTF-8')])
            ->first();

        if (! $promocod) {
            return response()->json([
                'message' => 'Промокод не найден.',
            ], 404);
        }

        $today = Carbon::today();

        if ($promocod->valid_from && $today->lt($promocod->valid_from)) {
            return response()->json([
                'message' => 'Промокод еще не начал действовать.',
            ], 422);
        }

        if ($promocod->valid_to && $today->gt($promocod->valid_to)) {
            return response()->json([
                'message' => 'Срок действия промокода истек.',
            ], 422);
        }

        if ($promocod->minimum_cart_amount > 0 && $cartSum < $promocod->minimum_cart_amount) {
            return response()->json([
                'message' => 'Сумма корзины меньше минимальной суммы для промокода.',
            ], 422);
        }

        $discount = 0;

        if ($promocod->discount_percent > 0) {
            $discount = round($cartSum * ($promocod->discount_percent / 100), 2);
        } else {
            $discount = (float) $promocod->fixed_discount_amount;
        }

        $finalAmount = max($cartSum - $discount, 0);

        return response()->json([
            'promo_code' => $promocod->promo_code,
            'discount' => $discount,
            'final_amount' => $finalAmount,
        ]);
    }
}
