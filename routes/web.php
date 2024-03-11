<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EasyPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VedomstvoController;

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

Route::get('/test', [TestController::class, "index"])->name('test');

Route::get('/vedomstva', [VedomstvoController::class, "index"])->name('vedomstva');
Route::get('/vedomstva/{slug}', [VedomstvoController::class, "vedomstvo"])->name('vedomstvo');
Route::get('/katalog', [CategoryController::class, "catalog"])->name('catalog');
Route::get('/katalog/{slug}', [CategoryController::class, "category"])->name('category');
Route::get('/product/{slug}', [ProductController::class, "show"])->name('product');

Route::get('/kontakty', [EasyPageController::class, "kontakty"])->name('kontakty');
Route::get('/proizvodstvo', [EasyPageController::class, "proizvodstvo"])->name('proizvodstvo');
Route::get('/optovye-postavki', [EasyPageController::class, "optovye_postavki"])->name('optovye-postavki');
