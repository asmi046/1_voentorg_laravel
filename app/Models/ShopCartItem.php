<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class ShopCartItem extends Model
{
    use AsSource;

    protected $table = 'shop_cart_items';

    protected $fillable = [
        'cart_id',
        'product_sku',
        'quantity',
        'price_snapshot',
        'raw_data',
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
        'raw_data' => 'array',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShopCart::class, 'cart_id');
    }
}
