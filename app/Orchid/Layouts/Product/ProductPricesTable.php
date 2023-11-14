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


class ProductPricesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'product_prices';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [

            TD::make('id', 'id'),

            TD::make('ed_izm', 'Единица измерения'),
            TD::make('volume', 'Объем'),
            TD::make('price', 'Цена'),
            TD::make('old_price', 'Старая цена'),


            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('100px')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.product_price_edit',$element->id)
                        ->icon('pencil'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm(__('Данная запись будет удален навсегда! Вы согласны?'))
                        ->method('delete_price', ["id" => $element->id]),
                ])),

        ];
    }
}
