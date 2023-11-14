<?php

namespace App\Orchid\Screens\Options;

use Orchid\Screen\Screen;

use App\Models\Option;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use Illuminate\Http\Request;

class EditOptions extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $option;

    public function query($id): iterable
    {
        return [
            "option" => Option::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование опции: '.$this->option->name;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $la[] = Layout::rows([
            Input::make('option.name')->value($this->option->name)->type('hidden'),
            Input::make('option.title')->value($this->option->title)->type('hidden'),
            Input::make('option.type')->value($this->option->type)->type('hidden'),

            Input::make('option.name')
                    ->title('Псевдоним')
                    ->help('Псевдоним записи')
                    ->required()
                    ->disabled()
                    ->horizontal(),

                Input::make('option.title')
                    ->title('Название')
                    ->help('Название опции')
                    ->required()
                    ->disabled()
                    ->horizontal(),
        ]);
        if ($this->option->type === "rich")
            $la[] = Layout::rows([
                Quill::make('option.value')->title('Значение')->required()->horizontal()->help('Введите значение'),
            ]);
        else
            $la[] = Layout::rows([
                TextArea::make('option.value')->title('Значение')->rows(6)->required()->horizontal()->help('Введите значение'),
            ]);

        $la[] = Layout::rows([
            Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
        ]);

        return $la;
    }

    public function save_info(Option $option, Request $request) {

        $new_data = $request->validate([
            'option.value' => ['required', 'string']
        ]);


        $this->option->fill($request->get('option'))->save();

        Toast::info("Запись сохранена");
    }
}
