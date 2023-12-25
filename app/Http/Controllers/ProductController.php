<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{


    public function show($slug) {

        $prosuct = Product::with("product_prices", "product_images")->where('slug', $slug)->first();

        if($prosuct == null) abort('404');

        $categories = $prosuct->tovar_categories()->first();

        return view('product', ['product' => $prosuct, 'category'=> $categories]);
    }
}
