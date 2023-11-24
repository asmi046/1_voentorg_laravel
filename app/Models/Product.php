<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;


use Orchid\Filters\Types\Like;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $fillable = [
        'sku',
        'group',
        'title',
        'slug',
        'img',
        'description',
        'short_description',
        'specification',
        'viev_count',
        'old_site_id',
        'hit',
        'new',
        'seo_title',
        'seo_description',
    ];

    protected $allowedSorts = [
        'id',
        'sku',
        'title'
    ];

    protected $allowedFilters  = [
        'title' => Like::class,
        'sku' => Like::class,
    ];


    public function scopeFilter(Builder $builder, QueryFilter $filter) {
        return $filter->apply($builder);
    }

    public function setSlugAttribute($value)
    {
        if (empty($value))
            $this->attributes['slug'] =  Str::slug($this->title);
        else
            $this->attributes['slug'] =  $value;
    }

    public function product_images() {
        return $this->hasMany(ProductImage::class);
    }

    public function product_prices() {
        return $this->hasMany(ProductPrices::class)->orderBy('price');
    }

    public function tovar_categories() {
        return $this->belongsToMany(Category::class);
    }
}
