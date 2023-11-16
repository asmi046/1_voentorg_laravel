<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Illuminate\Support\Str;
use Orchid\Filters\Types\Like;

class Category extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $fillable = [
        "title",
        "title_mini",
        "slug",
        "parent",
        "description",
        "img",
        "seo_title",
        "seo_description"
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
        if (empty($value))
            $this->attributes['slug'] =  Str::slug($this->title);
        else
            $this->attributes['slug'] =  $value;
    }

    public function category_tovars() {
        return $this->belongsToMany(Product::class);
    }

    public function parent_category() {
        return $this->hasOne(Category::class,"id","parent");
    }
}
