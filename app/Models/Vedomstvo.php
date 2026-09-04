<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

class Vedomstvo extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public $fillable = [
        'order',
        'title',
        'title_mini',
        'slug',
        'description',
        'img',
        'seo_title',
        'seo_description',
    ];

    protected $allowedSorts = [
        'title',
    ];

    protected $allowedFilters = [
        'title' => Like::class,
        'parent' => Like::class,
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }

    public function vedomstvo_tovars()
    {
        return $this->belongsToMany(Product::class);
    }
}
