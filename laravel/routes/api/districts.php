<?php

use App\Http\Controllers\Admin\District\DistrictController;
use Illuminate\Support\Facades\Route;

Route::prefix('cities/{city:uuid}/districts')->group(function () {
    Route::get('/', [DistrictController::class, 'index']);
});

Route::prefix('districts')->group(function () {
    Route::get('/{district:uuid}', [DistrictController::class, 'show']);
});