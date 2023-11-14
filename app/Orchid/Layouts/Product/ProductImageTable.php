<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Models\ProductImage;

use Orchid\Screen\Actions\DropDown;


class ProductImageTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'product_galery';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [

            TD::make('id', 'id'),
            TD::make('link', 'Фото')->render(
                function($element) {
                    return "<img width='50' height='50' src='".($element->link?$element->link:asset("img/noPhoto.jpg"))."'>";
                }
            ),
            TD::make('alt', 'Alt'),
            TD::make('title', 'Title'),

            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('100px')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.product_galery_edit',$element->id)
                        ->icon('pencil'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm(__('Данная запись будет удален навсегда! Вы согласны?'))
                        ->method('delete_galery_img', ["id" => $element->id]),
                ])),

        ];
    }
}
