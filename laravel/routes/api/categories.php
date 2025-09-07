<?php

use App\Http\Controllers\Admin\Category\CategoryController;
use Illuminate\Support\Facades\Route;

// Public routes for categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/roots', [CategoryController::class, 'roots'])->name('categories.roots');
Route::get('/categories/hierarchy', [CategoryController::class, 'hierarchy'])->name('categories.hierarchy');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/children', [CategoryController::class, 'children'])->name('categories.children');

// Admin routes for category management
Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('/categories/stats', [CategoryController::class, 'stats'])->name('admin.categories.stats');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::put('/categories/{category}/status', [CategoryController::class, 'updateStatus'])->name('admin.categories.update-status');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});
