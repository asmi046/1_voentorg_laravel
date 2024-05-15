<?php

namespace App\Orchid\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;

use App\Models\Category;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class ProductFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Поиск товара';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['category','sku','title'];
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
        ->where("sku", 'LIKE', '%'.$this->request->get('sku').'%')
        ->whereHas("tovar_categories", function (Builder $query) {
            $query->where('category_id',  $this->request->get('category'));
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
            ->placeholder('Наименование...')
            ->title('Имя товара'),

            Input::make('sku')
            ->type('text')
            ->value($this->request->get('sku'))
            ->placeholder('Введите артикул...')
            ->title('Артикул'),

            Select::make('category')
                ->fromModel(Category::class, 'title', 'id')
                ->empty('Все категории')
                ->title('Категория'),

        ];
    }
}
