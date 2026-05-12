<?php

use App\Http\Controllers\PromocodController;
use Illuminate\Support\Facades\Route;

Route::post('/promocod/verify', [PromocodController::class, 'verify'])->name('promocod_verify');