<?php

use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::get('/delivery/cities', [DeliveryController::class, 'cities'])->name('delivery_cities');
Route::post('/delivery/pickup-points', [DeliveryController::class, 'pickupPoints'])->name('delivery_pickup_points');
Route::post('/delivery/courier-offers', [DeliveryController::class, 'courierOffers'])->name('delivery_courier_offers');
