<?php

use App\Http\Controllers\Shop\ShopCartController;
use Illuminate\Support\Facades\Route;

Route::get('/bascet', [ShopCartController::class, 'page'])->name('bascet');
Route::get('/bascet/thencs', [ShopCartController::class, 'thencsPage'])->name('bascet_thencs');
