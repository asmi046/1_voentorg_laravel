<?php

namespace App\Http\Controllers;

use App\Models\Vedomstvo;

use Illuminate\Http\Request;
use App\Filters\ProductFilter;

class VedomstvoController extends Controller
{
    public function vedomstvo(ProductFilter $request, $slug) {
        $vedomstvo_info = Vedomstvo::with('vedomstvo_tovars')->where('slug', $slug)->first();

        return view('vedomstvo', ['vedomstvo_info' => $vedomstvo_info, 'tovars' =>
            $vedomstvo_info
            ->vedomstvo_tovars()
            ->orderBy('id', 'DESC')
            ->filter($request)
            ->get() ]);
    }

    public function index() {
        $all_vedomstva = Vedomstvo::all();

        return view('vedomstva', ['vedomstva' => $all_vedomstva]);
    }
}
