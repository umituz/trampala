<?php

use App\Http\Controllers\Listing\ListingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // User listing routes
    Route::get('/listings/my', [ListingController::class, 'my'])->name('listings.my');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    
    // Admin routes for listing management
    Route::middleware('role:Admin')->group(function () {
        Route::get('/listings/pending', [ListingController::class, 'pending'])->name('listings.pending');
        Route::post('/listings/{listing}/approve', [ListingController::class, 'approve'])->name('listings.approve');
        Route::post('/listings/{listing}/reject', [ListingController::class, 'reject'])->name('listings.reject');
        
        // Soft delete management (admin only)
        Route::post('/listings/{listing}/restore', [ListingController::class, 'restore'])
            ->name('listings.restore')
            ->withTrashed();
        Route::delete('/listings/{listing}/force-delete', [ListingController::class, 'forceDelete'])
            ->name('listings.force-delete')
            ->withTrashed();
    });
});

// Public routes
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');