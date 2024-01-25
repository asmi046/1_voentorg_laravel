<?php

namespace App\Orchid\Screens\Banner;

use Orchid\Screen\Screen;

use App\Models\Banner;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Orchid\Layouts\Banner\BannerEditFields;
use Orchid\Screen\Fields\Picture;

use Illuminate\Http\Request;

use Orchid\Screen\Fields\TextArea;

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
            "banner" => Banner::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
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

    public function save_info(Request $request) {

        $this->banner->fill($request->get('banner'))->save();

        Toast::info("Запись сохранена");
    }
}
