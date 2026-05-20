<?php

/**
 * SECURE AUTH ROUTES - Add to routes/api.php
 * Place these routes in your api.php file, in the appropriate sections
 */

// 🔐 ENHANCED AUTHENTICATION ROUTES (Add these to your routes/api.php)

// Public auth routes (no authentication required)
Route::post('/auth/refresh-token', [\App\Http\Controllers\Api\AuthControllerV2::class, 'refreshToken']);
Route::post('/auth/forgot-password', [\App\Http\Controllers\Api\AuthControllerV2::class, 'forgotPassword']);
Route::post('/auth/validate-reset-token', [\App\Http\Controllers\Api\AuthControllerV2::class, 'validateResetToken']);
Route::post('/auth/reset-password', [\App\Http\Controllers\Api\AuthControllerV2::class, 'resetPassword']);

// Protected auth routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Password management
    Route::post('/auth/change-password', [\App\Http\Controllers\Api\AuthControllerV2::class, 'changePassword']);
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthControllerV2::class, 'logout']);
    Route::post('/auth/logout-all-devices', [\App\Http\Controllers\Api\AuthControllerV2::class, 'logoutFromAllDevices']);
    
    // Session management
    Route::get('/auth/sessions', [\App\Http\Controllers\Api\AuthControllerV2::class, 'getActiveSessions']);
    Route::delete('/auth/sessions/{tokenId}', [\App\Http\Controllers\Api\AuthControllerV2::class, 'revokeSession']);
});
