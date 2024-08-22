<?php

namespace App\Orchid\Screens\News;

use Orchid\Screen\Screen;

use App\Models\News;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Orchid\Layouts\News\NewsEditFields;
use Orchid\Screen\Fields\Picture;

use Illuminate\Http\Request;

use Orchid\Screen\Fields\TextArea;

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
            "news" => News::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
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

    public function save_info(Request $request) {

        $this->news->fill($request->get('news'))->save();

        Toast::info("Запись сохранена");
    }
}
