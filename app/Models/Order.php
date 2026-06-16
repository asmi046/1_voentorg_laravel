<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Order extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $fillable = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
        'adress',
        'comment',
        'session_id',
        'user_id',
        'position_count',
        'amount',
        'base_summ',
        'discount_summ',
        'pay',
        'delivery',
    ];

    protected $allowedSorts = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
    ];

    protected $allowedFilters = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
    ];

    public static function update_order_pay_id($orderId, $payId ) {
        $element = self::where(["id" => $orderId])->first();
        $element->pay_order = $payId;
        $element->save();
    }

    public static function update_order_status($payId , $orderStatus, $orderStatusText) {
        $element = self::where(["pay_order" => $payId])->first();
        $element->pay_order = $payId;
        $element->pay_status = $orderStatus;
        $element->pay_status_text = $orderStatusText;
        $element->save();
    }

    public function orderProducts() {
        return $this->belongsToMany(Product::class);
    }

    public function orderCart() {
        return $this->hasMany(OrderProduct::class);
    }
}
