<?php

namespace App\Orchid\Screens\Category;

use Orchid\Screen\Screen;

use App\Models\Category;

use Orchid\Support\Facades\Layout;

use App\Orchid\Layouts\Category\CategoryEditFields;



use Illuminate\Http\Request;

class CategoryCreateScreen extends Screen
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
        return 'Создание категории';
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

    public function save_info(Request $request) {

        $request->validate([
            'category.title' => ['required', 'string'],
        ]);


        $new_cat_id = Category::create($request->get('category'));

        return redirect()->route('platform.category_edit', $new_cat_id);
    }
}
