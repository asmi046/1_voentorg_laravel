<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\ProductGroupPrice;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Celebration;

use Orchid\Screen\Actions\Link;

use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Validation\Rule;

use App\Orchid\Layouts\Product\ProductPriceEditFields;
use Illuminate\Http\Request;

class ProductPriceEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $element;

    public function query($id): iterable
    {
        $element = ProductGroupPrice::where('id', $id)->first();
        return [
            "element" => $element
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создание элемента галереи для продукта: '.$this->element->product_info->title;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
            ->href(route("platform.product_edit", $this->element->product_info->id))
            ->icon('arrow-up-left'),
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
            ProductPriceEditFields::class
        ];
    }


    public function save_info(Request $request) {
        $request->validate([
            'element.volume' => ['required', 'string'],
            'element.price' => ['required', 'string'],
            'element.old_price' => ['required', 'string'],
        ]);



        $this->element->fill($request->get('element'))->save();

        Toast::info("Запись сохранена");
    }
}
