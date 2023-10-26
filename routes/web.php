<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\EasyPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [IndexController::class, "show"])->name('home');

Route::get('/category', [EasyPageController::class, "category"])->name('category');
Route::get('/product', [EasyPageController::class, "product"])->name('product');

Route::get('/kontakty', [EasyPageController::class, "kontakty"])->name('kontakty');
Route::get('/proizvodstvo', [EasyPageController::class, "proizvodstvo"])->name('proizvodstvo');
Route::get('/optovye-postavki', [EasyPageController::class, "optovye_postavki"])->name('optovye-postavki');
