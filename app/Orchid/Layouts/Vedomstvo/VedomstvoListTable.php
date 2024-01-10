<?php

namespace App\Orchid\Layouts\Vedomstvo;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;

use Orchid\Screen\Actions\DropDown;

use App\Models\Vedomstvo;

use Orchid\Support\Color;

class VedomstvoListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'categories';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id')->width("10%"),

            TD::make('title', 'Заголовок')->width("30%"),
            TD::make('title_mini', 'Заголовок (короткий)')->width("30%"),

            TD::make('description', 'Описание')->width("35%")->render(function($element) {
                return  mb_strimwidth(strip_tags($element->description), 0, 30, "...");
            }),

            // TD::make('action', 'Действие')->render(function($element) {
            //     return  Group::make([
            //         Link::make('Редактировать')->route('platform.category_edit',$element->id),
            //         Button::make('Удалить')->method('delete_field', [$element->id])->type(Color::DANGER())
            //     ]);
            // })


            TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('100px')
            ->render(fn ($element) => DropDown::make()
                ->icon('chat-right-dots')
                ->list([

                    Link::make('Редактировать')
                        ->route('platform.vedomstva_edit',$element->id)
                        ->icon('pencil'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm(__('Данная категория будет удалена навсегда! Вы согласны?'))
                        ->method('delete_field', ["id" => $element->id]),
                ])),

        ];
    }
}
