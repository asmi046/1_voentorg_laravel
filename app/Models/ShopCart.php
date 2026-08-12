<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ShopCart extends Model
{
    use AsSource;
    use Filterable;

    protected $table = 'shop_carts';

    protected $fillable = [
        'session_id',
        'user_id',
    ];

    protected $allowedSorts = [
        'id',
        'created_at',
    ];

    protected $allowedFilters = [
        'session_id',
        'user_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ShopCartItem::class, 'cart_id');
    }
}
