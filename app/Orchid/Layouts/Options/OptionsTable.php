<?php

namespace App\Orchid\Layouts\Options;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;

use Orchid\Screen\Actions\DropDown;

class OptionsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'options';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id')->width("10%"),
            TD::make('name', 'Имя')->width("20%"),
            TD::make('title', 'Название')->filter(TD::FILTER_TEXT)->width("35%"),
            TD::make('value', 'Контент')->filter(TD::FILTER_TEXT)->width("30%")->render(function($element) {
                return  mb_strimwidth(strip_tags($element->value), 0, 30, "...");
            }),


            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('3%')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.options_edit',$element->id)
                        ->icon('pencil'),
                ])),
        ];
    }
}
