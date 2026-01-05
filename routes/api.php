<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductExternalController;

/*
|--------------------------------------------------------------------------
| API Routes - Halalytics
|--------------------------------------------------------------------------
*/

// ==========================================================
// 🔓 PUBLIC ROUTES (No Authentication Required)
// ==========================================================

// AUTH ENDPOINTS
Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);

// LOCAL PRODUCTS (from database)
Route::prefix('local')->group(function () {
    Route::get('/products', [ApiController::class, 'indexProduct']);
    Route::get('/products/search', [ApiController::class, 'searchProduct']);
    Route::get('/products/scan/{barcode}', [ApiController::class, 'scanProductByBarcode']);
    Route::get('/products/{id}', [ApiController::class, 'showProduct']);
    Route::get('/categories', [ApiController::class, 'indexKategori']);
});

// EXTERNAL PRODUCTS (OpenFoodFacts API)
Route::prefix('external')->group(function () {
    // Search endpoints
    Route::get('/search', [ProductExternalController::class, 'search']);
    Route::get('/halal', [ProductExternalController::class, 'halal']);
    Route::get('/vegetarian', [ProductExternalController::class, 'vegetarian']);
    Route::get('/vegan', [ProductExternalController::class, 'vegan']);
    
    // Search by attributes
    Route::get('/brand/{brand}', [ProductExternalController::class, 'brand']);
    Route::get('/category/{category}', [ProductExternalController::class, 'category']);
    
    // Product detail (must be last!)
    Route::get('/product/{barcode}', [ProductExternalController::class, 'detail']);
});

// ==========================================================
// 🔒 PROTECTED ROUTES (Require Authentication Token)
// ==========================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // USER PROFILE
    Route::prefix('user')->group(function () {
        Route::get('/profile', [ApiController::class, 'profile']);
        Route::post('/profile', [ApiController::class, 'updateProfile']);
        Route::post('/password', [ApiController::class, 'updatePassword']);
        Route::get('/stats', [ApiController::class, 'getUserStats']);
        Route::post('/logout', [ApiController::class, 'logout']);
    });
    
    // SCAN HISTORY
    Route::prefix('scans')->group(function () {
        Route::post('/', [ApiController::class, 'storeScan']);
        Route::get('/', [ApiController::class, 'indexMyScans']);
        Route::get('/history', [ApiController::class, 'getScanHistory']);
    });
    
    // REPORTS
    Route::prefix('reports')->group(function () {
        Route::post('/', [ApiController::class, 'storeReport']);
        Route::get('/', [ApiController::class, 'indexMyReports']);
    });
    
    // NOTIFICATIONS
    Route::get('/notifications', [ApiController::class, 'getNotifications']);
});

// ==========================================================
// 🔧 UTILITY ENDPOINTS
// ==========================================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Halalytics API is running',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0'
    ]);
});