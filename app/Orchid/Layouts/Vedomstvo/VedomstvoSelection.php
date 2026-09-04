<?php

namespace App\Orchid\Layouts\Vedomstvo;

use App\Orchid\Filters\Vedomstvo\VedomstvoFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class VedomstvoSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            VedomstvoFilter::class,
        ];
    }
}
