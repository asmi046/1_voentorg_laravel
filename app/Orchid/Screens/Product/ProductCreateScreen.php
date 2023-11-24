<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

use Illuminate\Validation\Rule;

use App\Orchid\Layouts\Product\ProductImageTable;

use App\Orchid\Layouts\Product\ProductEditFields;

use Illuminate\Http\Request;

class ProductCreateScreen extends Screen
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
        return 'Создание товара';
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
            ProductEditFields::class,
        ];
    }

    public function save_info(Request $request) {

        $request->validate([
            'product.title' => ['required', 'string'],
            'product.sku' => ['required', 'string'],
        ]);


        $new_id = Product::create($request->get('product'));

        $new_id->tovar_categories()->sync($request->get("category"));

        return redirect()->route('platform.product_edit', $new_id);
    }
}
