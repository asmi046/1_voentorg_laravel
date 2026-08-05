<?php

use App\Http\Controllers\CdekController;

Route::get('/cdek/cities', [CdekController::class, 'cities'])->name('cdek_cities');
Route::get('/cdek/delivery-points', [CdekController::class, 'deliveryPoints'])->name('cdek_delivery_points');
Route::post('/cdek/available-tariffs', [CdekController::class, 'availableTariffs'])->name('cdek_available_tariffs');
Route::get('/cdek/all-tariffs', [CdekController::class, 'allTariffs'])->name('cdek_all_tariffs');