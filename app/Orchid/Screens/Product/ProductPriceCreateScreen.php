<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\ProductPrices;
use App\Models\Product;
use App\Models\Celebration;

use Orchid\Screen\Actions\Link;

use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Validation\Rule;

use App\Orchid\Layouts\Product\ProductPriceEditFields;

use Illuminate\Http\Request;

class ProductPriceCreateScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $tovar;

    public function query($id): iterable
    {
        $tovar = Product::where('id', $id)->first();
        return [
            "tovar" => $tovar
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создание варианта цены для продукта: '.$this->tovar->title;
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
            ->href(route("platform.product_edit", $this->tovar->id))
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


    public function save_info( Request $request) {
        $request->validate([
            'element.sku' => ['required', 'string'],
            'element.price' => ['required', 'string'],
        ]);

        $data = $request->get('element');
        $data['product_id'] = $this->tovar->id;

        if ($data['old_price'] === null) $data['old_price'] = 0;

        $element_id = ProductPrices::create($data);

        return redirect()->route('platform.product_price_edit', $element_id);
    }
}
