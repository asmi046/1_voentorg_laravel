<?php

namespace App\Orchid\Screens\Category;

use Orchid\Screen\Screen;

use App\Models\Category;
use App\Orchid\Layouts\Category\CategoryListTable;
use App\Orchid\Layouts\Category\CategorySelection;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class CategoryListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        $cats = Category::with('parent_category')->filters(CategorySelection::class)->orderByDesc("created_at")->paginate(15);
        // dd($cats);
        return [
            "categories" => $cats
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Категории товаров';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить категорию')->route('platform.category_create')->type(Color::SUCCESS())
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
            CategorySelection::class,
            CategoryListTable::class
        ];
    }


    public function delete_field($id) {
        $dell_elem = Category::where('id', $id)->first();
        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Категория удалена");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }
}
