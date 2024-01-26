<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;

class OrderProduct extends Model
{
    use HasFactory;
    use AsSource;

    protected $fillable = [
        'product_sku',
        'quentity',
        'price',
    ];

    public function tovar_data() {
        return $this->hasOne(Product::class, 'sku', 'product_sku');
    }

}
