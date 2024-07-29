<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;

class Shop extends Model
{
    use HasFactory;
    use AsSource;

    protected $fillable = [
        'name',
        'adress',
        'geo',
        'orientir',
        'phone',
        'phone_2',
        'phone_3',
        'email',
        'time_work',
    ];

}
