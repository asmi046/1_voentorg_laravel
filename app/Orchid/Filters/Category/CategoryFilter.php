<?php

namespace App\Orchid\Filters\Category;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;

use Orchid\Screen\Fields\Input;

class CategoryFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Поиск категории: ';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['parent','title'];
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
        return $builder->where("title", 'LIKE', '%'.$this->request->get('title').'%')
        ->orWhereHas("parent_category", function (Builder $query) {
            $query->where('title', "LIKE", '%'.$this->request->get('parent').'%');
        });
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

            Input::make('parent')
            ->type('text')
            ->value($this->request->get('parent'))
            ->placeholder('Введите имя...')
            ->title('Имя родимельской категории')

        ];
    }
}
