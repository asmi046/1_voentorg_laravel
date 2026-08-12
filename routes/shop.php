<?php

use App\Http\Controllers\Shop\ShopCartController;
use Illuminate\Support\Facades\Route;

Route::prefix('shop')->group(function () {
    Route::prefix('cart')->name('shop.cart.')->group(function () {
        Route::get('/', [ShopCartController::class, 'get'])->name('get');
        Route::post('/add', [ShopCartController::class, 'add'])->name('add');
        Route::post('/update', [ShopCartController::class, 'update'])->name('update');
        Route::delete('/delete', [ShopCartController::class, 'delete'])->name('delete');
        Route::delete('/clear', [ShopCartController::class, 'clear'])->name('clear');
        Route::post('/checkout', [ShopCartController::class, 'checkout'])->name('checkout');
    });
});
