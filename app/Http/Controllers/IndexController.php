<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function show() {

        $all_product = Product::paginate(9);
        $sales_liders = Product::select()->orderBy('sales_count')->take(8)->get();
        $sales = Product::where('old_price', '!=', 0)->take(8)->get();

        // dd($all_product, $sales_liders,  $sales);

        return view('index', [
            'all_product' => $all_product,
            'sales_liders' => $sales_liders,
            'sales' => $sales,
        ]);
    }
}
