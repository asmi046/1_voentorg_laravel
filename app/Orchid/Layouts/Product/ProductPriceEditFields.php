<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Color;

class ProductPriceEditFields extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Input::make('element.product_group_id')
                ->type('hidden'),

            Input::make('element.sku')
                ->title('Артикул')
                ->required()
                ->horizontal(),

            Input::make('element.value')
                ->title('Характеристика')
                ->horizontal(),

            Input::make('element.price')
                ->title('Цена')
                ->required()
                ->horizontal(),

            Input::make('element.old_price')
                ->title('Старая цена')
                ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS()),
        ];
    }
}
