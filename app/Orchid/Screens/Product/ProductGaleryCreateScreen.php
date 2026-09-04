<?php

namespace App\Orchid\Screens\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Orchid\Layouts\Product\ProductGaleryEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ProductGaleryCreateScreen extends Screen
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
            'tovar' => $tovar,
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Создание элемента галереи для продукта: '.$this->tovar->title;
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
                ->href(route('platform.product_edit', $this->tovar->id))
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
            ProductGaleryEditFields::class,
        ];
    }

    public function save_info(Request $request)
    {
        $request->validate([
            'element.link' => ['required', 'string'],
        ]);

        $data = $request->get('element');
        $data['product_id'] = $this->tovar->id;

        $element_id = ProductImage::create($data);

        return redirect()->route('platform.product_galery_edit', $element_id);
    }
}
