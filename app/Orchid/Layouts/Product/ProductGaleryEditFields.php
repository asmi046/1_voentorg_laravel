<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Color;

class ProductGaleryEditFields extends Rows
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

            Input::make('element.alt')
                ->title('alt')
                ->help('Текст alt')
                ->horizontal(),

            Input::make('element.title')
                ->title('title')
                ->help('Текст title')
                ->horizontal(),

            Picture::make('element.link')
                ->title('Основное изображение')
                ->storage('public')
                ->targetRelativeUrl()
                ->required()
                ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS()),
        ];
    }
}
