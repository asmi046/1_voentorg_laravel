<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Banner extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'img',
        'title',
        'sub_title',
    ];

    protected $allowedSorts = [
        'title',
    ];
}
