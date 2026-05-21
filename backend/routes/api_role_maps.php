<?php

use App\Http\Controllers\API\Nutritionist\NutritionistDashboardController;
use App\Http\Controllers\API\User\UserDashboardController;
use App\Http\Controllers\API\User\UserScanController;
use App\Http\Controllers\API\ProductDetailController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index']);
    Route::post('/scan', [UserScanController::class, 'scan']);
    Route::get('/product-detail', [ProductDetailController::class, 'show']);
});

Route::prefix('nutritionist')->middleware(['auth:sanctum', 'role:ahli_gizi'])->group(function () {
    Route::get('/dashboard', [NutritionistDashboardController::class, 'index']);
});
