<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Vedomstvo;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function show() {

        $all_product = Product::paginate(9);
        $sales_liders = Product::whereHas("product_prices",
            function (Builder $query) {
                $query->where('price', "!=", 0);
            }
        )->orderBy('viev_count')->take(4)->get();
        $sales = Product::whereHas("product_prices",
        function (Builder $query) {
            $query->where('old_price', "!=", 0);
        }
    )->take(8)->get();

        $category = Category::where("parent", 0)->orWhere("parent", NULL) ->get();

        $vedomstva = Vedomstvo::select("*")->take(6)->get();
        // dd($all_product, $sales_liders,  $sales);

        return view('index', [
            'all_product' => $all_product,
            'sales_liders' => $sales_liders,
            'sales' => $sales,
            'category' => $category,
            'vedomstva' => $vedomstva
        ]);
    }
}
