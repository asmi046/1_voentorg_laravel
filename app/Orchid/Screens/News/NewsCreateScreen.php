<?php

namespace App\Orchid\Screens\News;

use App\Models\News;
use App\Orchid\Layouts\News\NewsEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;

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

    public function save_info(Request $request)
    {

        $new_cat_id = News::create($request->get('news'));

        return redirect()->route('platform.news_edit', $new_cat_id);
    }
}
