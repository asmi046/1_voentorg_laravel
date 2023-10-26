<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;

class EasyPageController extends Controller
{
    public function policy() {
        return view('policy');
    }

    public function kontakty() {
        return view('kontakty');
    }

    public function proizvodstvo() {
        return view('proizvodstvo');
    }

    public function optovye_postavki() {
        return view('optovye-postavki');
    }
}
