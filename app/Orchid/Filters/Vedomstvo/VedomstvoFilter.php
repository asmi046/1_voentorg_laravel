<?php

namespace App\Orchid\Filters\Vedomstvo;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;

use Orchid\Screen\Fields\Input;

class VedomstvoFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Поиск ведомства: ';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['title'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where("title", 'LIKE', '%'.$this->request->get('title').'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [

            Input::make('title')
            ->type('text')
            ->value($this->request->get('title'))
            ->placeholder('Введите имя...')
            ->title('Имя категории'),
        ];
    }
}
