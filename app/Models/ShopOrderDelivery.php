<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class ShopOrderDelivery extends Model
{
    use AsSource;

    protected $table = 'shop_order_deliveries';

    protected $fillable = [
        'order_id',
        'provider',
        'method',
        'price',
        'tariff',
        'delivery_date_range',
        'city',
        'pickup_point_id',
        'pickup_point_address',
        'delivery_address',
        'street',
        'house',
        'apartment',
        'raw_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'delivery_date_range' => 'array',
        'raw_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }
}
