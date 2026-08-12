<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class ShopOrderItem extends Model
{
    use AsSource;

    protected $table = 'shop_order_items';

    protected $fillable = [
        'order_id',
        'product_sku',
        'product_name',
        'product_title',
        'price',
        'quantity',
        'weight_grams',
        'dimensions',
        'raw_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'dimensions' => 'array',
        'raw_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }
}
