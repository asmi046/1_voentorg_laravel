<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

use App\Filters\ProductFilter;

class SearchController extends Controller
{

    public function show_search_page(ProductFilter $request) {
        $search_str = $request->request->get('search_str');

        $request->request->flash();

        $tovars = Product::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        // ->orWhere('product_prices.sku', 'LIKE', "%".$search_str."%")
        ->filter($request)
        ->paginate(15)->withQueryString();

        return view('search', ['tovars' => $tovars, 'search_str' => $search_str]);
    }

    public function search_pds(Request $request) {
        $result_array = ["products" => [], "brand" => [], "categories" => [], "img_prefix" => Storage::url('')];

        $search_str = $request->get('search_str');

        if (empty($search_str)) return $result_array;

        $products = Product::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->take(30)
        ->get();

        $cat = Category::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->take(5)
        ->get();

        $brand = Brand::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->take(5)
        ->get();

        $result_array["brand"] = $brand;

        $result_array["categories"] = $cat;

        $result_array["products"] = $products;

        return $result_array;
    }
}
