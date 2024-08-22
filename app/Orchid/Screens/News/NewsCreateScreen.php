<?php

namespace App\Orchid\Screens\News;

use Orchid\Screen\Screen;

use App\Models\News;

use Orchid\Support\Facades\Layout;

use App\Orchid\Layouts\News\NewsEditFields;



use Illuminate\Http\Request;

class NewsCreateScreen extends Screen
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
        return 'Создание новости';
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
            NewsEditFields::class,
        ];
    }

    public function save_info(Request $request) {

        $new_cat_id = News::create($request->get('news'));

        return redirect()->route('platform.news_edit', $new_cat_id);
    }
}
