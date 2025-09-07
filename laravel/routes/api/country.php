<?php

use App\Http\Controllers\Admin\Country\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('countries')->group(function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/{country:uuid}', [CountryController::class, 'show']);
    Route::get('/{country:uuid}/cities', [CountryController::class, 'cities']);
});
