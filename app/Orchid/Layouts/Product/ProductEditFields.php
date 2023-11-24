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
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;

use App\Models\Category;
use App\Models\ColorEffect;

class ProductEditFields extends Rows
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

            Input::make('product.title')
                ->title('Заголовок')
                ->required()
                ->horizontal(),

            Input::make('product.sku')
                ->title('Артикул')
                ->required()
                ->horizontal(),

            Input::make('product.group')
                ->title('Группа товаров')
                ->horizontal(),

            Relation::make('category.')
                ->fromModel(Category::class, 'title', 'id')
                ->title('Категории товара')
                ->multiple()
                ->horizontal()
                ->help('Выберите категорию'),

            Input::make('product.slug')
                ->title('Окончание ссылки')
                ->horizontal(),

            Input::make('product.viev_count')
                ->title('Количество просмотров')
                ->horizontal(),

            Switcher::make('product.hit')
                ->sendTrueOrFalse()
                ->title('Хит продаж')
                ->placeholder('Хит продаж')
                ->horizontal(),

            Switcher::make('product.new')
                ->sendTrueOrFalse()
                ->title('Новинка')
                ->placeholder('Новинка')
                ->horizontal(),

            Quill::make('product.description')
                ->title('Описание')
                ->help('Введите описание товара')
                ->horizontal(),

            Quill::make('product.short_description')
                ->title('Краткое описание')
                ->help('Введите краткое описание товара')
                ->horizontal(),

            Quill::make('product.specification')
                ->title('Спецификация')
                ->help('Введите спецификацию товара')
                ->horizontal(),

            Picture::make('product.img')
                ->title('Основное изображение')
                ->storage('public')
                ->targetRelativeUrl()
                ->horizontal(),


            Input::make('product.seo_title')
                    ->title('SEO заголовок')
                    ->help('SEO заголовок')
                    ->horizontal(),

            TextArea::make('product.seo_description')
                    ->title('SEO описание')
                    ->help('SEO описание')
                    ->horizontal(),

            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ];
    }
}
