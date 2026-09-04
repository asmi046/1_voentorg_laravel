<?php

namespace App\Orchid\Screens\Shop;

use App\Models\Shop;
use App\Orchid\Layouts\Shop\ShopEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ShopEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public $shop;

    public function query($id): iterable
    {
        return [
            'shop' => Shop::where('id', $id)->first(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Редактирование магазина: '.$this->shop->title;
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

        $this->shop->fill($request->get('shop'))->save();

        Toast::info('Запись сохранена');
    }
}
