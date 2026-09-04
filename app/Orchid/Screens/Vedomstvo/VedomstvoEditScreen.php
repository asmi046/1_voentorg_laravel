<?php

namespace App\Orchid\Screens\Vedomstvo;

use App\Models\Vedomstvo;
use App\Orchid\Layouts\Vedomstvo\VedomstvoEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class VedomstvoEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public $category;

    public function query($id): iterable
    {
        return [
            'category' => Vedomstvo::where('id', $id)->first(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Редактирование ведомства: '.$this->category->title;
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
        return [

            VedomstvoEditFields::class,
        ];
    }

    public function save_info(Request $request)
    {

        $this->category->fill($request->get('category'))->save();

        Toast::info('Запись сохранена');
    }
}
