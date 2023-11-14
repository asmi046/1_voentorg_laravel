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
        $tovar = ProductGroupPrice::where('product_group_id', $id)->first();
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
        return 'Создание ценового предложения для продукта: '.$this->tovar->product_info->title;
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
            ->href(route("platform.product_edit", $this->tovar->product_info->id))
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
            'element.volume' => ['required', 'string'],
            'element.price' => ['required', 'string'],
            'element.old_price' => ['required', 'string'],
        ]);

        $data = $request->get('element');
        $data['product_group_id'] = $this->tovar->product_info->id;

        $element_id = ProductGroupPrice::create($data);

        return redirect()->route('platform.product_price_edit', $element_id);
    }
}
