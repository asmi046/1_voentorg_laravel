<?php

namespace App\Orchid\Screens\Product;

use App\Models\ProductPrices;
use App\Orchid\Layouts\Product\ProductPriceEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

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
        $element = ProductPrices::where('id', $id)->first();

        return [
            'element' => $element,
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Редактирование варианта цены для продукта: '.$this->element->product_info->title;
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
                ->href(route('platform.product_edit', $this->element->product_info->id))
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
            ProductPriceEditFields::class,
        ];
    }

    public function save_info(Request $request)
    {
        $request->validate([
            'element.sku' => ['required', 'string'],
            'element.price' => ['required', 'string'],
        ]);

        $this->element->fill($request->get('element'))->save();

        Toast::info('Запись сохранена');
    }
}
