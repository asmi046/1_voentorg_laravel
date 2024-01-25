<?php

namespace App\Orchid\Screens\Banner;

use Orchid\Screen\Screen;

use App\Models\Banner;

use Orchid\Support\Facades\Layout;

use App\Orchid\Layouts\Banner\BannerEditFields;



use Illuminate\Http\Request;

class BannerCreateScreen extends Screen
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
        return 'Создание баннера';
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

        $new_cat_id = Banner::create($request->get('banner'));

        return redirect()->route('platform.banner_edit', $new_cat_id);
    }
}
