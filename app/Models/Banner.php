<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Banner extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    protected $fillable = [
        'img',
        'title',
        'sub_title',
    ];

    protected $allowedSorts = [
        'title'
    ];
}
