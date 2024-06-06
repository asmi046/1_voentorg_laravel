<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vedomstvo;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class IndexController extends Controller
{
    public function show() {

        $all_product = Product::paginate(9);
        $sales_liders = Product::whereHas("product_prices",
            function (Builder $query) {
                $query->where('price', "!=", 0);
            }
        )->orderBy('viev_count')->take(6)->get();

        $sales = Product::whereHas("product_prices",
            function (Builder $query) {
                $query->where('old_price', "!=", 0);
            }
        )->take(8)->get();

        $news = Product::where("new", 1)->take(10)->get();

        $category = Category::where("parent", 0)->orWhere("parent", NULL) ->get();

        $vedomstva = Vedomstvo::select("*")->take(8)->get();
        // dd($all_product, $sales_liders,  $sales);

        $banners = Banner::all();

        return view('index', [
            'all_product' => $all_product,
            'sales_liders' => $sales_liders,
            'sales' => $sales,
            'news' => $news,
            'category' => $category,
            'banners' => $banners,
            'vedomstva' => $vedomstva
        ]);
    }
}
