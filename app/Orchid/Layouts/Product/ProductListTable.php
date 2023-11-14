<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;

use Orchid\Screen\Actions\DropDown;

use App\Models\Category;

use Orchid\Support\Color;

class ProductListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'products';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id')->sort(),
            TD::make('img', 'Фото')->render(
                function($element) {
                    return "<img width='50' height='50' src='".($element->img?$element->img:asset("img/no_photo.jpg"))."'>";
                }
            )->width("10%"),
            TD::make('sku', 'Артикул')->width("20%")->sort(),
            TD::make('category', 'Категории')->render(function($element) {
                $rstr = "";
                foreach($element->category_tovars as $item) {
                    $rstr .= $item->name."<br/>";
                }

                return $rstr;
            })->width("20%"),
            TD::make('tm', 'Торговая арка')->sort()->width("15%"),

            TD::make('title', 'Заголовок')->width("45%")->filter(TD::FILTER_TEXT),

            // TD::make('slug', 'Ссылка'),
            // TD::make('description', 'Описание')->render(function($element) {
            //     return  mb_strimwidth(strip_tags($element->description), 0, 30, "...");
            // }),

            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('100px')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.product_edit',$element->id)
                        ->icon('pencil'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm(__('Данный продукт будет удален навсегда! Вы согласны?'))
                        ->method('delete_field', ["id" => $element->id]),
                ])),

        ];
    }
}
