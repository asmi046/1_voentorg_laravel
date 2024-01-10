<?php

namespace App\Orchid\Screens\Vedomstvo;

use Orchid\Screen\Screen;

use App\Models\Vedomstvo;
use App\Orchid\Layouts\Vedomstvo\VedomstvoListTable;
use App\Orchid\Layouts\Vedomstvo\VedomstvoSelection;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class VedomstvoListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        $cats = Vedomstvo::filters(VedomstvoSelection::class)->orderByDesc("created_at")->paginate(15);
        // dd($cats);
        return [
            "categories" => $cats
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ведомства';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить ведомства')->route('platform.vedomstva_create')->type(Color::SUCCESS())
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            VedomstvoSelection::class,
            VedomstvoListTable::class
        ];
    }


    public function delete_field($id) {
        $dell_elem = Vedomstvo::where('id', $id)->first();
        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Запись удалена");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }
}
