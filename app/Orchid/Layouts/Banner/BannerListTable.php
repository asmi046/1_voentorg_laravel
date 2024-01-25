<?php

namespace App\Orchid\Layouts\Banner;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;

use Orchid\Screen\Actions\DropDown;

class BannerListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'banners';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id')->width("10%"),
            TD::make('img', 'Изображение')->render(
                function($element) {
                    return "<img height='50' src='".($element->img?$element->img:asset("img/noPhoto.jpg"))."'>";
                }
            )->width("28%"),
            TD::make('title', 'Заголовок')->width("28%"),
            TD::make('sub_title', 'Подзаголовок')->width("28%"),


            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('3%')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.banner_edit', $element->id)
                        ->icon('pencil'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm(__('Данная запись будет удалена навсегда! Вы согласны?'))
                        ->method('delete_field', ["id" => $element->id]),
                ])),
        ];
    }
}
