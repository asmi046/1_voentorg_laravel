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
                ->help('Заголовок товара')
                ->required()
                ->horizontal(),

            Input::make('product.tm')
                ->title('Торговая марка')
                ->help('Торговая марка товара')
                ->horizontal(),

            Switcher::make('product.popular')
                ->sendTrueOrFalse()
                ->title('Популярный товар')
                ->placeholder('Популярный товар')
                ->horizontal()
                ->help('Разместить товар в популярных'),


            Input::make('product.sku')
                ->title('Артикул')
                ->required()
                ->help('Артикул товара')
                ->required()
                ->horizontal(),


            Input::make('product.slug')
                ->title('Окончание ссылки')
                ->help('Slug категории')
                ->horizontal(),

            Quill::make('product.description')
                ->title('Описание')
                ->help('Введите описание товара')
                ->horizontal(),

            Quill::make('product.short_description')
                ->title('Краткое описание')
                ->help('Введите краткое описание товара')
                ->horizontal(),

            Relation::make('category.')
                ->fromModel(Category::class, 'name', 'id')
                ->title('Категории товара')
                ->multiple()
                ->horizontal()
                ->help('Выберите категорию'),

            Relation::make('effect.')
                ->fromModel(ColorEffect::class, 'name', 'id')
                ->title('Визуальный эффект')
                ->multiple()
                ->horizontal()
                ->help('Выберите эффект'),

            Matrix::make('product.props')
                ->title('Свойства')
                ->horizontal()
                ->columns([
                    'Параметр',
                    'Величина',
                ]),

            Matrix::make('product.advantages')
                ->title('Преимущества')
                ->horizontal()
                ->columns([
                    'Преимущество',
                ]),

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
