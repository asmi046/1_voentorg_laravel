<?php

namespace App\Orchid\Layouts\Category;

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

use App\Models\Category;

class CategoryEditFields extends Rows
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

            Input::make('category.title')
                ->title('Заголовок')
                ->help('Заголовок категории')
                ->required()
                ->horizontal(),

            Input::make('category.title_mini')
                ->title('Заголовок (короткий)')
                ->help('Короткий заголовок категории')
                ->horizontal(),


            Input::make('category.slug')
                ->title('Окончание ссылки')
                ->help('Slug категории')
                ->horizontal(),


            Picture::make('category.img')
                ->title('Основное изображение')
                ->storage('public')
                ->targetRelativeUrl()
                ->horizontal(),

            Select::make('category.parent')
                ->empty('Не выбрано')
                ->fromQuery(Category::where('id', '!=', 'category.parent'), 'title', 'id')
                ->title('Родительская категория')
                ->help('Выберите родительскую категорию')
                ->horizontal(),

            Quill::make('category.description')
                ->title('Описание категории')
                ->help('Введите описание категории')
                ->horizontal(),

            Input::make('category.seo_title')
                    ->title('SEO заголовок')
                    ->help('SEO заголовок')
                    ->horizontal(),

            TextArea::make('category.seo_description')
                    ->title('SEO описание')
                    ->help('SEO описание')
                    ->horizontal(),
            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ];
    }
}
