<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocod extends Model
{
    use HasFactory;

    protected $table = 'promocods';

    protected $fillable = [
        'promo_type',
        'promo_code',
        'valid_from',
        'valid_to',
        'discount_percent',
        'fixed_discount_amount',
        'minimum_cart_amount',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'discount_percent' => 'integer',
        'fixed_discount_amount' => 'integer',
        'minimum_cart_amount' => 'integer',
    ];
}
