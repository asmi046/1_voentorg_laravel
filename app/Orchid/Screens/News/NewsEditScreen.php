<?php

namespace App\Orchid\Screens\News;

use App\Models\News;
use App\Orchid\Layouts\News\NewsEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class NewsEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public $news;

    public function query($id): iterable
    {
        return [
            'news' => News::where('id', $id)->first(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Редактирование новости: '.$this->news->title;
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

        $this->news->fill($request->get('news'))->save();

        Toast::info('Запись сохранена');
    }
}
