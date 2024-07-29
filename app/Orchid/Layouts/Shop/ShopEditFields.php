<?php

namespace App\Orchid\Layouts\Shop;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Number;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Fields\Picture;

class ShopEditFields extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title = "Поля категории";

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [

            Input::make('shop.name')
                ->title('Название')
                ->horizontal(),

            Input::make('shop.adress')
                ->title('Адрес')
                ->required()
                ->horizontal(),

            Input::make('shop.geo')
                ->title('Координаты на карте')
                ->required()
                ->horizontal(),


            Input::make('shop.orientir')
                ->title('Ориентир на местности')
                ->horizontal(),

            Input::make('shop.phone')
                ->title('Телефон')
                ->horizontal(),

            Input::make('shop.phone_2')
                ->title('Телефон 2')
                ->horizontal(),

            Input::make('shop.phone_3')
                ->title('Телефон 3')
                ->horizontal(),

            Input::make('shop.email')
                ->title('E-mail')
                ->horizontal(),

            Input::make('shop.time_work')
                ->title('Время работы')
                ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ];
    }
}
