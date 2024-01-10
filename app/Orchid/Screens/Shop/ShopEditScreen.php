<?php

namespace App\Orchid\Screens\Shop;

use Orchid\Screen\Screen;

use App\Models\Shop;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Orchid\Layouts\Shop\ShopEditFields;
use Orchid\Screen\Fields\Picture;

use Illuminate\Http\Request;

use Orchid\Screen\Fields\TextArea;

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
            "shop" => Shop::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
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

    public function save_info(Request $request) {

        $this->category->fill($request->get('shop'))->save();

        Toast::info("Запись сохранена");
    }
}
