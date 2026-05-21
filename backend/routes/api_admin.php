<?php

use App\Http\Controllers\API\Admin\AILogsController;
use App\Http\Controllers\API\Admin\AIPromptManagerController;
use App\Http\Controllers\API\Admin\AdminDashboardController;
use App\Http\Controllers\API\Admin\HalalRulesManagerController;
use App\Http\Controllers\API\Admin\NutritionRulesManagerController;
use App\Http\Controllers\API\Admin\PartnerShowcaseController;
use App\Http\Controllers\API\Admin\AITrainingController;
use App\Http\Controllers\API\Admin\MedicalRulesManagerController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::get('/ai-prompts', [AIPromptManagerController::class, 'index']);
    Route::post('/ai-prompts', [AIPromptManagerController::class, 'store']);

    Route::get('/halal-rules', [HalalRulesManagerController::class, 'index']);
    Route::post('/halal-rules', [HalalRulesManagerController::class, 'store']);
    Route::put('/halal-rules/{id}', [HalalRulesManagerController::class, 'update']);
    Route::delete('/halal-rules/{id}', [HalalRulesManagerController::class, 'destroy']);

    Route::get('/nutrition-rules', [NutritionRulesManagerController::class, 'index']);
    Route::post('/nutrition-rules', [NutritionRulesManagerController::class, 'store']);
    Route::put('/nutrition-rules/{id}', [NutritionRulesManagerController::class, 'update']);
    Route::delete('/nutrition-rules/{id}', [NutritionRulesManagerController::class, 'destroy']);

    Route::get('/ai-logs', [AILogsController::class, 'index']);
    Route::post('/ai-feedbacks', [AILogsController::class, 'submitFeedback']);
    Route::get('/ai-analytics', [AILogsController::class, 'analytics']);
    Route::get('/ai-training-dataset', [AITrainingController::class, 'dataset']);
    Route::get('/partners/preview', [PartnerShowcaseController::class, 'preview']);

    Route::get('/medical-rules', [MedicalRulesManagerController::class, 'index']);
    Route::post('/medical-rules/symptoms', [MedicalRulesManagerController::class, 'storeSymptomRule']);
    Route::put('/medical-rules/symptoms/{id}', [MedicalRulesManagerController::class, 'updateSymptomRule']);
    Route::delete('/medical-rules/symptoms/{id}', [MedicalRulesManagerController::class, 'destroySymptomRule']);
    Route::post('/medical-rules/releases', [MedicalRulesManagerController::class, 'publishRelease']);
});
