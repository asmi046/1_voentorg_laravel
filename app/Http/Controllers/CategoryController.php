<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function catalog() {
        $category = Category::where("parent", 0)->get();

        return view("catalog", [
            'category' => $category
        ]);
    }

    public function category($slug) {
        $category_info = Category::with("category_tovars")->where("slug", $slug)->first();
        $product = $category_info->category_tovars;
        return view("category", [
            'category_info' => $category_info,
            'tovars' => $product
        ]);
    }
}
