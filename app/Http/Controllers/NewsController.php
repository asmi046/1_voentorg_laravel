<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index() {
        $news = News::paginate(10);
        return view('news.all', ['news' => $news]);
    }

    public function news_page($slug) {
        $page = News::where("slug", $slug)->first();

        if($page == null) abort('404');

        return view('news.news_page', ['page' => $page]);
    }
}
