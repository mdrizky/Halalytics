<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductExternalController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MobileSyncController;
use App\Http\Controllers\Api\FoodRecognitionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ScanHistoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UnifiedScanController;
use App\Http\Controllers\Api\EncyclopediaController;
use App\Http\Controllers\Api\AIAssistantController;
use App\Http\Controllers\Api\DrugInteractionController;
use App\Http\Controllers\Api\PillIdentificationController;
use App\Http\Controllers\Api\LabAnalysisController;
use App\Http\Controllers\Api\MedicationReminderController;
use App\Http\Controllers\Api\HalalAlternativeController;
use App\Http\Controllers\Api\HealthMetricController;


/*
|--------------------------------------------------------------------------
| API Routes - Halalytics
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('products/{barcode}', [ProductController::class, 'show']);
    Route::post('products/check-halal', [ProductController::class, 'checkHalal']);
    Route::post('products/batch-check-halal', [ProductController::class, 'batchCheckHalal']);
    Route::get('products/alternatives/{barcode}', [ProductController::class, 'alternatives']);
});

// ==========================================================
// 📱 ANDROID COMPATIBILITY ROUTES
// ==========================================================
// These routes handle legacy endpoints called by the current Android build
Route::get('products/barcode/{barcode}', [ApiController::class, 'scanProductByBarcode']);
Route::get('products/search', [ApiController::class, 'searchProduct']);

// Temporary test route for AI Verification
Route::post('/test-analyze-symptoms', [\App\Http\Controllers\Api\MedicineController::class, 'analyzeSymptoms']);

// ==========================================================
// 🔓 PUBLIC ROUTES
// ==========================================================

// AUTH
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);

// BANNERS
Route::get('/banners', [\App\Http\Controllers\Api\BannerController::class, 'index']);

// LOCAL PRODUCTS (from database)
Route::prefix('local')->group(function () {
    Route::get('/products', [ApiController::class, 'indexProduct']);
    Route::get('/products/search', [ApiController::class, 'searchProduct']);
    Route::get('/products/scan/{barcode}', [ApiController::class, 'scanProductByBarcode']);
    Route::get('/products/{id}', [ApiController::class, 'showProduct']);
    Route::get('/categories', [ApiController::class, 'indexKategori']);
    
    // ENCYCLOPEDIA
    Route::get('/encyclopedia', [EncyclopediaController::class, 'index']);
    Route::get('/encyclopedia/{id}', [EncyclopediaController::class, 'show']);
    Route::get('/encyclopedia/e-number/{eNumber}', [EncyclopediaController::class, 'searchByENumber']);
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
    // Advanced Health Suite Hub
    Route::post("/lab-results/upload", [App\Http\Controllers\Api\LabResultController::class, "uploadAndAnalyze"]);
    Route::post("/nutrition-scans", [App\Http\Controllers\Api\NutritionScanController::class, "scan"]);
    Route::get("/medical-records", [App\Http\Controllers\Api\MedicalRecordController::class, "index"]);
    Route::post("/medical-records", [App\Http\Controllers\Api\MedicalRecordController::class, "store"]);
    Route::post("/emergency/trigger", [App\Http\Controllers\Api\EmergencyController::class, "triggerEmergency"]);

    
    // AUTH LOGOUT
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // ADMIN ROUTES
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\AdminController::class, 'getDashboardStats']);
        Route::get('/products/pending', [\App\Http\Controllers\Api\AdminController::class, 'getPendingProducts']);
        Route::put('/products/{id}/approve', [\App\Http\Controllers\Api\AdminController::class, 'approveProduct']);
        Route::put('/products/{id}/reject', [\App\Http\Controllers\Api\AdminController::class, 'rejectProduct']);
    });

    // USER ROUTES (Health & Scan)
    Route::prefix('user')->group(function () {
        Route::post('/scan', [\App\Http\Controllers\Api\UserController::class, 'scanProduct']);
        Route::get('/daily-intake', [\App\Http\Controllers\Api\UserController::class, 'getDailyIntake']);
        
        // FAMILY BOX (Multi-profile)
        Route::prefix('family')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\FamilyController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\FamilyController::class, 'store']);
            Route::post('/{id}', [\App\Http\Controllers\Api\FamilyController::class, 'update']); // Use POST with _method=PUT for image upload support
            Route::delete('/{id}', [\App\Http\Controllers\Api\FamilyController::class, 'destroy']);
        });
    });

    // USER PROFILE
    Route::prefix('user')->group(function () {
        Route::get('/profile', [ApiController::class, 'profile']);
        Route::post('/profile', [ApiController::class, 'updateProfile']);
        Route::post('/allergies', [ApiController::class, 'updateAllergies']);
        Route::post('/password', [ApiController::class, 'updatePassword']);
        Route::get('/stats', [ApiController::class, 'getUserStats']);
        Route::get('/stats/weekly', [ApiController::class, 'getWeeklyStats']);
        Route::post('/logout', [ApiController::class, 'logout']);
        
        // NEW PROFILE FEATURES
        Route::get('/achievements', [\App\Http\Controllers\Api\ProfileFeatureController::class, 'getAchievements']);
        Route::post('/export-report', [\App\Http\Controllers\Api\ProfileFeatureController::class, 'exportMonthlyReport']);
    });
    
    // SCAN HISTORY
    Route::prefix('scans')->group(function () {
        Route::post('/', [ApiController::class, 'storeScan']);
        Route::get('/', [ApiController::class, 'indexMyScans']);
        Route::get('/history', [ApiController::class, 'getScanHistory']);
        Route::post('/add', [ApiController::class, 'addScanHistory']); // New route for adding scan history
    });
    
    // REPORTS
    Route::prefix('reports')->group(function () {
        Route::post('/', [ApiController::class, 'storeReport']);
        Route::get('/', [ApiController::class, 'indexMyReports']);
    });
    
    // NOTIFICATIONS
    Route::get('/notifications', [ApiController::class, 'getNotifications']);
    
    // STREET FOOD RECOGNITION (AI)
    Route::prefix('food')->group(function () {
        Route::post('/search', [FoodRecognitionController::class, 'search']);
        Route::post('/analyze', [FoodRecognitionController::class, 'analyze']);
        Route::post('/recognize-image', [FoodRecognitionController::class, 'recognizeImage']); // NEW
        Route::get('/popular', [FoodRecognitionController::class, 'popular']);
        Route::get('/categories', [FoodRecognitionController::class, 'categories']);
        Route::get('/user-logs', [FoodRecognitionController::class, 'userLogs']);
        Route::get('/{id}', [FoodRecognitionController::class, 'show']);
    });

    // FCM TOKEN REGISTRATION
    Route::post('/fcm/register', [\App\Http\Controllers\Api\FcmController::class, 'register']);

    // UMKM SCAN
    Route::post('/umkm/scan-qr', [\App\Http\Controllers\Api\UmkmScanController::class, 'scanQR']);
    Route::get('/umkm/nearby', [ApiController::class, 'getNearbyUmkm']);

    // VERIFICATION REQUESTS
    Route::post('/products/request-verification', [ApiController::class, 'requestVerification']);

    // RECOMMENDATIONS
    Route::get('/products/recommendations', [ApiController::class, 'getRecommendations']);

    // ========== NOTIFICATIONS ==========
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // ========== SCAN HISTORY ==========
    Route::prefix('scan-history')->group(function () {
        Route::get('/', [ScanHistoryController::class, 'index']);
        Route::post('/', [ScanHistoryController::class, 'recordScan']);
        Route::delete('/{id}', [ScanHistoryController::class, 'destroy']);
    });

    // ========== FAVORITES ==========
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/', [FavoriteController::class, 'store']);
        Route::delete('/{id}', [FavoriteController::class, 'destroy']);
        Route::put('/{id}/notes', [FavoriteController::class, 'updateNotes']);
    });

    // UNIFIED SCAN
    Route::post('/scan/unified', [UnifiedScanController::class, 'scan']);

    // AI ASSISTANT
    Route::post('/ai/analyze', [AIAssistantController::class, 'analyzeIngredients']);
    Route::get('/ai/weekly-report', [AIAssistantController::class, 'generateWeeklyReport']);
    Route::get('/ai/daily-intake', [AIAssistantController::class, 'getDailyIntake']);
    Route::get('/ai/personal-risk-score', [AIAssistantController::class, 'getPersonalRiskScore']);

    // CONTRIBUTIONS
    Route::prefix('contributions')->group(function () {
        Route::post('/submit', [\App\Http\Controllers\Api\ContributionController::class, 'submit']);
        Route::get('/my', [\App\Http\Controllers\Api\ContributionController::class, 'myContributions']);
        Route::get('/admin/all', [\App\Http\Controllers\Api\ContributionController::class, 'indexAll']);
    });

    // Product Contributions (requires auth)
    Route::post('/product-requests', [App\Http\Controllers\Api\ProductRequestController::class, 'store']);

    // AI MEAL SCANNER
    Route::post('/meal/analyze', [\App\Http\Controllers\Api\MealAiController::class, 'analyzeMeal']);

    // MEDICINE REMINDER & CHECKER
    Route::prefix('medicines')->group(function () {
        Route::post('/check', [\App\Http\Controllers\Api\MedicineController::class, 'checkHalal']);
        Route::post('/schedule', [\App\Http\Controllers\Api\MedicineController::class, 'addToSchedule']);
        Route::post('/safe-schedule', [\App\Http\Controllers\Api\MedicineController::class, 'generateSafeSchedule']);
        Route::post('/drug-food-conflict', [\App\Http\Controllers\Api\MedicineController::class, 'checkDrugFoodConflict']);
        Route::get('/my', [\App\Http\Controllers\Api\MedicineController::class, 'getUserMedicines']);
        
        // AI Health Assistant Routes
        Route::post('/analyze-symptoms', [\App\Http\Controllers\Api\MedicineController::class, 'analyzeSymptoms']);
        Route::post('/search', [\App\Http\Controllers\Api\MedicineController::class, 'searchMedicine']);
        Route::post('/reminders', [\App\Http\Controllers\Api\MedicineController::class, 'createReminder']);
        Route::get('/reminders/{userId}', [\App\Http\Controllers\Api\MedicineController::class, 'getUserReminders']);
        Route::post('/reminders/mark-taken', [\App\Http\Controllers\Api\MedicineController::class, 'markAsTaken']);
        Route::get('/reminders/{userId}/next-dose', [\App\Http\Controllers\Api\MedicineController::class, 'getNextDose']);
        Route::get('/{id}', [\App\Http\Controllers\Api\MedicineController::class, 'show']);
    });

    // ADMIN MONITOR
    Route::prefix('admin/monitor')->middleware('role:admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Api\AdminMonitorController::class, 'getDashboardStats']);
        Route::get('/feed', [\App\Http\Controllers\Api\AdminMonitorController::class, 'getActivityFeed']);
        Route::put('/medicines/{id}/status', [\App\Http\Controllers\Api\AdminMonitorController::class, 'updateMedicineStatus']);
    });

    // HALAL CERTIFICATE VALIDATOR
    Route::prefix('certificate')->group(function () {
        Route::post('/verify', [\App\Http\Controllers\Api\CertificateValidatorController::class, 'verify']);
        Route::get('/history', [\App\Http\Controllers\Api\CertificateValidatorController::class, 'history']);
    });

    // OCR SCANNER
    Route::prefix('ocr')->group(function () {
        Route::post('/submit', [\App\Http\Controllers\Api\OCRController::class, 'submitOCR']);
        Route::get('/history/{id}', [\App\Http\Controllers\Api\OCRController::class, 'getUserOCRHistory']);
        Route::get('/statistics', [\App\Http\Controllers\Api\OCRController::class, 'getOCRStatistics']);
    });

    // AI ADVANCED HEALTH FEATURES
    Route::prefix('ai')->group(function () {
        Route::post('/interactions', [DrugInteractionController::class, 'check']);
        Route::get('/drugs/search', [DrugInteractionController::class, 'search']);
        Route::post('/pill-identify', [PillIdentificationController::class, 'identify']);
        Route::post('/lab-analysis', [LabAnalysisController::class, 'analyze']);
        Route::get('/lab-history', [LabAnalysisController::class, 'history']);
        Route::post('/reminders', [MedicationReminderController::class, 'store']);
        Route::post('/reminders/log', [MedicationReminderController::class, 'log']);
        Route::get('/reminders', [MedicationReminderController::class, 'index']);
        Route::delete('/reminders/{id}', [MedicationReminderController::class, 'destroy']);
        Route::get('/halal-alternatives', [HalalAlternativeController::class, 'getAlternatives']);
        Route::post('/compare', [\App\Http\Controllers\Api\ComparisonController::class, 'compare']);
    });

    // HEALTH TRACKING (Health Journey)
    Route::prefix('health')->group(function () {
        Route::post('/metrics', [HealthMetricController::class, 'store']);
        Route::get('/metrics/history', [HealthMetricController::class, 'history']);
        Route::get('/metrics/summary', [HealthMetricController::class, 'summary']);
        Route::post('/analyze', [HealthMetricController::class, 'analyze']);
    });

    // ========== BPOM VERIFICATION ==========
    Route::prefix('bpom')->group(function () {
        Route::get('/search', [\App\Http\Controllers\Api\BpomController::class, 'searchBpom']);
        Route::post('/check', [\App\Http\Controllers\Api\BpomController::class, 'checkRegistration']);
        Route::post('/analyze', [\App\Http\Controllers\Api\BpomController::class, 'analyzeProduct']);
    });

    // ========== SKINCARE / KOSMETIK ANALYSIS ==========
    Route::prefix('skincare')->group(function () {
        Route::post('/analyze', [\App\Http\Controllers\Api\SkincareController::class, 'analyzeIngredients']);
        Route::post('/safety', [\App\Http\Controllers\Api\SkincareController::class, 'checkSafety']);
        Route::post('/halal', [\App\Http\Controllers\Api\SkincareController::class, 'getHalalStatus']);
    });
});

// ==========================================================
// 🔧 UTILITY ENDPOINTS
// ==========================================================

// MOBILE SYNC ENDPOINTS (For Admin Integration)
Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
    Route::post('/sync/scans', [MobileSyncController::class, 'syncScanData']);
    Route::post('/sync/users', [MobileSyncController::class, 'syncUserData']);
    Route::get('/products', [MobileSyncController::class, 'getProducts']);
    Route::get('/categories', [MobileSyncController::class, 'getCategories']);
});

// ADMIN STATISTICS ENDPOINTS
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/stats/users', [MobileSyncController::class, 'getUserStats']);
    Route::get('/stats/scans', [MobileSyncController::class, 'getScanStats']);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Halalytics API is running',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0'
    ]);
});
