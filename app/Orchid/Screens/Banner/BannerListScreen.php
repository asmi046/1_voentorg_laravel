<?php

namespace App\Orchid\Screens\Banner;

use App\Models\Banner;
use App\Orchid\Layouts\Banner\BannerListTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class BannerListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        $banners = Banner::all();

        return [
            'banners' => $banners,
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Баннер';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить баннер')->route('platform.banner_create')->type(Color::SUCCESS()),
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
            BannerListTable::class,
        ];
    }

    public function delete_field($id)
    {
        $dell_elem = Banner::where('id', $id)->first();
        if ($dell_elem) {
            $dell_elem->delete();
            Toast::info('Запись удалена');
        } else {
            Toast::info('Ошибка при удалении');
        }
    }
}
