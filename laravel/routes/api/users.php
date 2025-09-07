<?php

use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    // User management routes for admin
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/stats', [UserController::class, 'stats'])->name('admin.users.stats');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.update-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});