<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OCRController;

// OCR Routes
Route::prefix('ocr')->group(function () {
    
    // Upload and process OCR image
    Route::post('/upload', [OCRController::class, 'uploadImage']);
    
    // Get OCR products for admin review
    Route::get('/pending', [OCRController::class, 'getPendingProducts'])
        ->middleware('auth');
    
    // Approve OCR product
    Route::post('/approve/{id}', [OCRController::class, 'approveProduct'])
        ->middleware('auth');
    
    // Reject OCR product
    Route::post('/reject/{id}', [OCRController::class, 'rejectProduct'])
        ->middleware('auth');
    
    // Get OCR statistics
    Route::get('/statistics', [OCRController::class, 'getStatistics'])
        ->middleware('auth');
    
    // Get OCR product details
    Route::get('/product/{id}', [OCRController::class, 'getOCRProduct'])
        ->middleware('auth');
});
