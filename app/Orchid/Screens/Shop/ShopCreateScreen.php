<?php

namespace App\Orchid\Screens\Shop;

use App\Models\Shop;
use App\Orchid\Layouts\Shop\ShopEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;

class ShopCreateScreen extends Screen
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
     */
    public function name(): ?string
    {
        return 'Создание магазина';
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
            ShopEditFields::class,
        ];
    }

    public function save_info(Request $request)
    {

        $new_cat_id = Shop::create($request->get('shop'));

        return redirect()->route('platform.shops_edit', $new_cat_id);
    }
}
