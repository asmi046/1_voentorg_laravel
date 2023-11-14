<?php

namespace App\Orchid\Layouts\Category;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\Category\CategoryFilter;

class CategorySelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            CategoryFilter::class
        ];
    }
}
