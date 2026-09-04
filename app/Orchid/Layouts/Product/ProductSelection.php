<?php

namespace App\Orchid\Layouts\Product;

use App\Orchid\Filters\Product\ProductFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ProductSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            ProductFilter::class,
        ];
    }
}
