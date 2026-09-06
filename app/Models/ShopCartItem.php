<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
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

    public function productPrice(): BelongsTo
    {
        return $this->belongsTo(ProductPrices::class, 'product_sku', 'sku');
    }

    /**
     * Связанный Product через таблицу product_prices.
     *
     * Цепочка: shop_cart_items.product_sku -> product_prices.sku -> product_prices.product_id -> products.id.
     * Это корректно для всех вариантов (включая размеры), в отличие от belongsTo(Product::class, 'product_sku', 'sku'),
     * который срабатывает только когда sku варианта случайно совпадает с Product.sku.
     */
    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,
            ProductPrices::class,
            'sku',
            'id',
            'product_sku',
            'product_id',
        );
    }
}
