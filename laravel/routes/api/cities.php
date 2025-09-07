<?php

use App\Http\Controllers\Admin\City\CityController;
use Illuminate\Support\Facades\Route;

// Public routes for cities
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
Route::get('/cities/{city}', [CityController::class, 'show'])->name('cities.show');
