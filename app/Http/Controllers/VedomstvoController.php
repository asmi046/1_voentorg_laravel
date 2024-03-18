<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Vedomstvo;

class VedomstvoController extends Controller
{
    public function vedomstvo($slug) {
        $vedomstvo_info = Vedomstvo::with('vedomstvo_tovars')->where('slug', $slug)->first();

        return view('vedomstvo', ['vedomstvo_info' => $vedomstvo_info, 'tovars' => $vedomstvo_info->vedomstvo_tovars()->orderBy('updated_at', "ASC")->get() ]);
    }

    public function index() {
        $all_vedomstva = Vedomstvo::all();

        return view('vedomstva', ['vedomstva' => $all_vedomstva]);
    }
}
