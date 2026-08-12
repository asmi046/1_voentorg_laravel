<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ShopOrder extends Model
{
    use AsSource;
    use Filterable;

    protected $table = 'shop_orders';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'comment',
        'promo_code',
        'cart_summ',
        'discount_summ',
        'total_summ',
        'payment_id',
        'payment_status',
        'payment_status_text',
        'session_id',
        'user_id',
    ];

    protected $casts = [
        'cart_summ' => 'decimal:2',
        'discount_summ' => 'decimal:2',
        'total_summ' => 'decimal:2',
    ];

    protected $allowedSorts = [
        'id',
        'created_at',
        'name',
        'phone',
        'total_summ',
        'payment_status',
    ];

    protected $allowedFilters = [
        'id',
        'name',
        'email',
        'phone',
        'promo_code',
        'payment_status',
        'session_id',
        'user_id',
    ];

    public function delivery(): HasOne
    {
        return $this->hasOne(ShopOrderDelivery::class, 'order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'order_id');
    }
}
