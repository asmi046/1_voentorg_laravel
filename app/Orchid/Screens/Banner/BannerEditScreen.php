<?php

namespace App\Orchid\Screens\Banner;

use App\Models\Banner;
use App\Orchid\Layouts\Banner\BannerEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class BannerEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public $banner;

    public function query($id): iterable
    {
        return [
            'banner' => Banner::where('id', $id)->first(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Редактирование баннера: '.$this->banner->title;
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

            BannerEditFields::class,
        ];
    }

    public function save_info(Request $request)
    {

        $this->banner->fill($request->get('banner'))->save();

        Toast::info('Запись сохранена');
    }
}
