<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Order extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $fillable = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
        'adress',
        'comment',
        'session_id',
        'user_id',
        'position_count',
        'amount',
        'pay',
        'delivery',
    ];

    protected $allowedSorts = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
    ];

    protected $allowedFilters = [
        'id',
        'created_at',
        'name',
        'email',
        'phone',
    ];

    public function orderProducts() {
        return $this->belongsToMany(Product::class);
    }

    public function orderCart() {
        return $this->hasMany(OrderProduct::class);
    }
}
