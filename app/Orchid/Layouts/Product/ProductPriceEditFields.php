<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;


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
            Input::make("element.product_group_id")
            ->type('hidden'),

            Select::make('element.ed_izm')
                ->options([
                    'шт'   => 'шт',
                    'л' => 'л',
                    'кг' => 'кг',
                ])
                ->title('Единица')
                ->help('Единица измерения')
                ->horizontal(),

            Input::make('element.volume')
                ->title('Значение')
                ->required()
                ->help('Значение единицы')
                ->horizontal(),

            Input::make('element.price')
                ->title('Цена')
                ->required()
                ->help('Действующая цена')
                ->horizontal(),

            Input::make('element.old_price')
                ->title('Старая цена')
                ->help('Цена до скидки')
                ->required()
                ->horizontal(),


            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ];
    }
}
