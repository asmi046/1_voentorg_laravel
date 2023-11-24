<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\Product\ProductFilter;

class ProductSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            ProductFilter::class
        ];
    }
}
