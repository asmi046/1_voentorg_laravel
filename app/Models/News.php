<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class News extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'img',
        'description',
        'short_description',
        'seo_title',
        'seo_description',
    ];

    protected $allowedSorts = [
        'title',
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }
}
