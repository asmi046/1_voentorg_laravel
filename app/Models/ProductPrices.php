<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;

class ProductPrices extends Model
{
    use HasFactory;
    use AsSource;


    public $fillable = [
        'product_id',
        'sku',
        'value',
        'price',
        'old_price',
    ];
}
