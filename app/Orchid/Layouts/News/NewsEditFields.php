<?php

namespace App\Orchid\Layouts\News;

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

class NewsEditFields extends Rows
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


            Input::make('news.title')
                ->title('Название')
                ->required()
                ->horizontal(),

            Input::make('news.slug')
                ->title('Окончание ссылки')
                ->horizontal(),

            Picture::make('news.img')
                ->title('Изображение')
                ->storage('public')
                ->targetRelativeUrl()
                ->required()
                ->horizontal(),

            Quill::make('news.short_description')
                ->title('Краткое описание')
                ->required()
                ->help('Введите краткое описание товара')
                ->horizontal(),

            Quill::make('news.description')
                ->title('Описание')
                ->required()
                ->help('Введите описание товара')
                ->horizontal(),

            Input::make('news.seo_title')
                ->title('SEO заголовок')
                ->help('SEO заголовок')
                ->horizontal(),

            TextArea::make('news.seo_description')
                ->title('SEO описание')
                ->help('SEO описание')
                ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ];
    }
}
