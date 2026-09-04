<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Category;

class CategoryController extends Controller
{
    public function catalog()
    {
        $category = Category::where('parent', 0)->orWhere('parent', null)->get();

        return view('catalog', [
            'category' => $category,
        ]);
    }

    public function category(ProductFilter $request, $slug)
    {
        $category_info = Category::with('category_tovars')->where('slug', $slug)->first();
        $sub_category = Category::where('parent', $category_info->id)->get();

        $product = $category_info
            ->category_tovars()
            ->filter($request)
            ->paginate(16)->withQueryString();

        return view('category', [
            'category_info' => $category_info,
            'sub_category' => $sub_category,
            'tovars' => $product,
        ]);
    }
}
