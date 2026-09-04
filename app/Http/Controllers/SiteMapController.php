<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vedomstvo;

class SiteMapController extends Controller
{
    public function index()
    {
        $all_product = Product::all();
        $all_category = Category::all();
        $all_vedomstvo = Vedomstvo::all();

        return response()->view('sitemap.sitemap', [
            'product' => $all_product,
            'category' => $all_category,
            'vedomstvo' => $all_vedomstvo,
        ])->header('Content-Type', 'text/xml');
    }
}
