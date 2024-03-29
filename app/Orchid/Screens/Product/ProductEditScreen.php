<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Celebration;

use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Actions\Link;
use Illuminate\Validation\Rule;

use App\Orchid\Layouts\Product\ProductEditFields;
use App\Orchid\Layouts\Product\ProductImageTable;
use App\Orchid\Layouts\Product\ProductPricesTable;

use Illuminate\Http\Request;

class ProductEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $product;
     public $product_galery;
     public $product_prices;
     public $category;
     public $vedomstvo;
     public $effect;

    public function query($id): iterable
    {
        $product = Product::where('id',$id)->first();

        $product_galery = $product->product_images;
        $product_prices = $product->product_prices;

        $category = $product->tovar_categories;
        $vedomstvo = $product->tovar_vedomstva;
        $effect = $product->effects;

        // dd($category, $effect);

        return [
            "product" => $product,
            "category"=> $category,
            "vedomstvo"=> $vedomstvo,
            "effect"=> $effect,
            "product_galery"=> $product_galery,
            "product_prices"=> $product_prices,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование продукта: '.$this->product->title;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить в галерею')
            ->href(route("platform.product_galery_create", $this->product->id))
            ->icon('card-image'),

            Link::make('Добавить цену')
            ->href(route("platform.product_price_create", $this->product->id))
            ->icon('coin'),

            Link::make('Просмотр')
            ->href(route("product", $this->product->slug))
            ->target('_blanck')
            ->icon('aspect-ratio'),
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

            Layout::tabs([
                'Параметры товарной группы' => [
                    ProductEditFields::class,
                ],

                'Галерея'      => [
                    ProductImageTable::class
                ],

                'Варианты цены'      => [
                    ProductPricesTable::class
                ],
            ]),


        ];
    }

    public function delete_galery_img($id) {

        $dell_elem = $this->product_galery->where('id', $id)->first();

        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Запись удалена");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }

    public function delete_price($id) {
        $dell_elem = $this->product_prices->where('id', $id)->first();
        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Запись удалена");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }

    public function save_info( Request $request) {

        $request->validate([
            'product.title' => ['required', 'string'],
            'product.sku' => ['required', 'string'],
        ]);

        $this->product->tovar_categories()->sync($request->get("category"));
        $this->product->tovar_vedomstva()->sync($request->get("vedomstvo"));

        $this->product->fill($request->get('product'))->save();

        Toast::info("Продукт сохранен");
    }
}
