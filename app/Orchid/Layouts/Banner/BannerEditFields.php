<?php

namespace App\Orchid\Layouts\Banner;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Color;

class BannerEditFields extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title = 'Поля категории';

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Picture::make('banner.img')
                ->title('Изображение')
                ->storage('public')
                ->targetRelativeUrl()
                ->required()
                ->horizontal(),

            Input::make('banner.title')
                ->title('Название')
                ->required()
                ->horizontal(),

            Input::make('banner.sub_title')
                ->title('Адрес')
                ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS()),
        ];
    }
}
