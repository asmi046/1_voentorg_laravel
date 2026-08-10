<?php

use App\Http\Controllers\CdekController;
use Illuminate\Support\Facades\Route;

Route::get('/cdek/cities', [CdekController::class, 'cities'])->name('cdek_cities');
Route::get('/cdek/delivery-points', [CdekController::class, 'deliveryPoints'])->name('cdek_delivery_points');
Route::post('/cdek/available-tariffs', [CdekController::class, 'availableTariffs'])->name('cdek_available_tariffs');
Route::post('/cdek/best-pickup-point-tariff', [CdekController::class, 'bestPickupPointTariff'])->name('cdek_best_pickup_point_tariff');
Route::post('/cdek/best-tariff-by-mode', [CdekController::class, 'bestTariffByMode'])->name('cdek_best_tariff_by_mode');
Route::get('/cdek/all-tariffs', [CdekController::class, 'allTariffs'])->name('cdek_all_tariffs');
