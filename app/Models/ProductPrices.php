<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ProductPrices extends Model
{
    use AsSource;
    use HasFactory;

    public $fillable = [
        'product_id',
        'sku',
        'ext_id',
        'category_id',
        'value',
        'count',
        'price',
        'old_price',
    ];

    public function product_info()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
