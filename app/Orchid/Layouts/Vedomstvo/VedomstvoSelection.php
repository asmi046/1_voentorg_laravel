<?php

namespace App\Orchid\Layouts\Vedomstvo;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\Vedomstvo\VedomstvoFilter;

class VedomstvoSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            VedomstvoFilter::class
        ];
    }
}
