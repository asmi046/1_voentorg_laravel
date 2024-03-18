<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function catalog() {
        $category = Category::where("parent", 0)->orWhere("parent", NULL)->get();

        return view("catalog", [
            'category' => $category
        ]);
    }

    public function category($slug) {
        $category_info = Category::with("category_tovars")->where("slug", $slug)->first();
        $sub_category = Category::where("parent", $category_info->id)->get();

        $product = $category_info->category_tovars()->orderBy('updated_at', "ASC")->paginate(16)->withQueryString();
        return view("category", [
            'category_info' => $category_info,
            'sub_category' => $sub_category,
            'tovars' => $product
        ]);
    }
}
