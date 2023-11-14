<?php

namespace App\Orchid\Screens\Category;

use Orchid\Screen\Screen;

use App\Models\Category;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Orchid\Layouts\Category\CategoryEditFields;
use Orchid\Screen\Fields\Picture;

use Illuminate\Http\Request;

use Orchid\Screen\Fields\TextArea;

class CategoryEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $category;

    public function query($id): iterable
    {
        return [
            "category" => Category::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование категории: '.$this->category->title;
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

            CategoryEditFields::class,
        ];
    }

    public function save_info(Category $category, Request $request) {

        $request->validate([
            'category.title' => ['required', 'string'],
        ]);


        $this->category->fill($request->get('category'))->save();

        Toast::info("Запись сохранена");
    }
}
