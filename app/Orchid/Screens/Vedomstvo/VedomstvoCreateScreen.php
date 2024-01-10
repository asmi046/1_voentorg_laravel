<?php

namespace App\Orchid\Screens\Vedomstvo;

use Orchid\Screen\Screen;

use App\Models\Vedomstvo;

use Orchid\Support\Facades\Layout;

use App\Orchid\Layouts\Vedomstvo\VedomstvoEditFields;



use Illuminate\Http\Request;

class VedomstvoCreateScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

    public function query(): iterable
    {
        return [];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создание ведомства';
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

    public function save_info(Request $request) {

        $new_cat_id = Vedomstvo::create($request->get('category'));

        return redirect()->route('platform.vedomstva_edit', $new_cat_id);
    }
}
