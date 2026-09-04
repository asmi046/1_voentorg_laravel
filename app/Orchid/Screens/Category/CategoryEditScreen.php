<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Orchid\Layouts\Category\CategoryEditFields;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

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
            'category' => Category::where('id', $id)->first(),
        ];
    }

    /**
     * Display header name.
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

    public function save_info(Category $category, Request $request)
    {

        $request->validate([
            'category.title' => ['required', 'string'],
        ]);

        $this->category->fill($request->get('category'))->save();

        Toast::info('Запись сохранена');
    }
}
