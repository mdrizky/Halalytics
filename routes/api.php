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
use App\Http\Controllers\Api\HealthArticleController;
use App\Http\Controllers\Api\UserHealthInsightController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\HealthEncyclopediaController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\OCRController;
use App\Http\Controllers\Api\NutritionController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\BpomController;
use App\Http\Controllers\Api\SkincareController;
use App\Http\Controllers\Api\MentalHealthController;
use App\Http\Controllers\Api\HelpCenterController;
use App\Http\Controllers\Api\AuthControllerV2;
use App\Http\Controllers\Api\SkincareController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\MentalHealthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\NutritionistDashboardController;
use App\Http\Controllers\Api\NutritionConsultationController;

/*
|--------------------------------------------------------------------------
| Halalytics API Routes - Consolidated & Cleaned
|--------------------------------------------------------------------------
*/

// 🔓 PUBLIC ROUTES
Route::post('/payment/webhook/midtrans', [\App\Http\Controllers\Api\PaymentWebhookController::class, 'midtrans']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);
Route::post('/auth/facebook', [AuthController::class, 'facebookLogin']);
Route::post('/auth/sync', [AuthController::class, 'syncUser']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);

// HEALTH CONTENT
Route::get('/articles', [HealthArticleController::class, 'index']);
Route::get('/articles/recommended', [HealthArticleController::class, 'recommended']);
Route::get('/articles/{slug}', [HealthArticleController::class, 'show']);
Route::get('/encyclopedia', [EncyclopediaController::class, 'index']);
Route::get('/encyclopedia/{id}', [EncyclopediaController::class, 'show']);
Route::get('/health-encyclopedia', [\App\Http\Controllers\Api\HealthEncyclopediaController::class, 'index']);
Route::get('/health-encyclopedia/{id}', [\App\Http\Controllers\Api\HealthEncyclopediaController::class, 'show']);
Route::prefix('mental-health')->group(function () {
    Route::get('/topics', [MentalHealthController::class, 'topics']);
    Route::get('/articles', [MentalHealthController::class, 'articles']);
    Route::get('/experts', [MentalHealthController::class, 'experts']);
    Route::get('/questions/{type}', [MentalHealthController::class, 'getQuestions']);
});
Route::prefix('help')->group(function () {
    Route::get('/categories', [HelpCenterController::class, 'categories']);
    Route::get('/faq', [HelpCenterController::class, 'faq']);
    Route::post('/request', [HelpCenterController::class, 'submitRequest']);
});

// PRODUCTS (Hybrid Search)
Route::prefix('products')->group(function () {
    Route::get('/barcode/{barcode}', [ProductController::class, 'show']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/popular', [ProductController::class, 'popular']);
    Route::get('/recommendations', [ProductController::class, 'recommendations']);
    Route::get('/external/{barcode}', [ProductExternalController::class, 'detail']);
    Route::get('/product-detail', [ProductController::class, 'detailProduct']);
});
Route::prefix('v1/products')->group(function () {
    Route::get('/popular', [ProductController::class, 'popular']);
});

// Product Comparison
Route::middleware('auth:sanctum')->post('/products/compare', [\App\Http\Controllers\Api\ProductComparisonController::class, 'compare']);

// 🔒 PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    
    // USER & PROFILE
    Route::prefix('user')->group(function () {
        Route::get('/profile', [ApiController::class, 'profile']);
        Route::get('/get-profile', [\App\Http\Controllers\Api\UserController::class, 'getProfile']); // Added for more standard endpoint
        Route::post('/profile', [ApiController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/stats', [ApiController::class, 'getUserStats']);
        Route::get('/daily-insight', [UserHealthInsightController::class, 'getDailyInsight']);
        Route::post('/change-password', [AuthControllerV2::class, 'changePassword']);
    });

    // SCAN & HISTORY
    Route::post('/scan/unified', [UnifiedScanController::class, 'scan']);
    Route::prefix('scans')->group(function () {
        Route::get('/history', [ScanHistoryController::class, 'index']);
        Route::get('/{id}', [ScanHistoryController::class, 'show']);
        Route::post('/record', [ScanHistoryController::class, 'recordScan']);
        Route::delete('/{id}', [ScanHistoryController::class, 'destroy']);
    });

    // OCR & INGREDIENTS (Consolidated)
    Route::prefix('ocr')->group(function () {
        Route::post('/submit', [OCRController::class, 'submitOCR']);
        Route::get('/history', [OCRController::class, 'history']);
        Route::get('/sync', [OCRController::class, 'syncIngredients']);
        Route::post('/scan-result', [OCRController::class, 'scanResult']);
        Route::post('/save', [OCRController::class, 'scanResult']);
    });

    // MEDICINES & REMINDERS
    Route::prefix('medicines')->group(function () {
        Route::get('/', [MedicineController::class, 'index']);
        Route::post('/search', [MedicineController::class, 'searchMedicine']);
        Route::post('/analyze-symptoms', [MedicineController::class, 'analyzeSymptoms']);
        Route::post('/schedule', [MedicineController::class, 'generateSafeSchedule']);
        Route::post('/check', [MedicineController::class, 'checkHalal']);
        Route::get('/reminders', [MedicationReminderController::class, 'index']);
        Route::post('/reminders', [MedicationReminderController::class, 'store']);
        Route::post('/reminders/log', [MedicationReminderController::class, 'log']);
        Route::get('/reminders/next-dose', [MedicationReminderController::class, 'nextDose']);
        Route::delete('/reminders/{id}', [MedicationReminderController::class, 'destroy']);
        Route::get('/{id}', [MedicineController::class, 'show']);
    });

    // NUTRITION & MEALS
    Route::prefix('nutrition')->group(function () {
        Route::post('/log', [NutritionController::class, 'logMeal']);
        Route::get('/daily', [NutritionController::class, 'getDailyLog']);
        Route::get('/history', [NutritionController::class, 'getHistory']);
        Route::get('/goals', [NutritionController::class, 'getGoals']);
        Route::post('/goals', [NutritionController::class, 'setGoals']);
    });

    // BPOM & SKINCARE
    Route::prefix('bpom')->group(function () {
        Route::get('/search', [BpomController::class, 'search']);
        Route::post('/check', [BpomController::class, 'check']);
        Route::post('/analyze', [BpomController::class, 'analyze']);
    });
    Route::prefix('skincare')->group(function () {
        Route::post('/analyze', [SkincareController::class, 'analyze']);
        Route::post('/safety', [SkincareController::class, 'safetyCheck']);
        Route::post('/halal-check', [SkincareController::class, 'getHalalStatus']);
    });

    // HALAL ALTERNATIVES
    Route::get('/products/alternatives/{barcode}', [HalalAlternativeController::class, 'index']);

    // COMMUNITY
    Route::prefix('community')->group(function () {
        Route::get('/posts', [CommunityController::class, 'index']);
        Route::post('/posts', [CommunityController::class, 'store']);
        Route::get('/posts/{id}', [CommunityController::class, 'show']);
        Route::post('/posts/{id}/like', [CommunityController::class, 'likePost']);
        Route::post('/posts/{id}/comment', [CommunityController::class, 'comment']);
    });

    // HALOCODE (Expert Consultations) - REMOVED FOR PRODUCTION CLEANUP

    // ═══════════════════════════════════════════════════════════
    // 📱 MOBILE APP ROUTES (Previously missing — causing APK 404s)
    // ═══════════════════════════════════════════════════════════

    // NOTIFICATIONS
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    });

    // SCAN HISTORY (Realtime)
    Route::prefix('scan-history')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ScanHistoryController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ScanHistoryController::class, 'recordScan']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ScanHistoryController::class, 'show']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\ScanHistoryController::class, 'destroy']);
    });

    // FAVORITES
    Route::prefix('favorites')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FavoriteController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\FavoriteController::class, 'store']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'destroy']);
        Route::put('/{id}/notes', [\App\Http\Controllers\Api\FavoriteController::class, 'updateNotes']);
    });

    // Offline & health batch sync (mobile WorkManager)
    Route::post('/sync/scan-logs', [SyncController::class, 'syncScanLogs']);
    Route::post('/sync/health-logs', [SyncController::class, 'syncHealthLogs']);

    // User ↔ ahli gizi (konsultasi dasar, siap dikembangkan ke realtime)
    Route::prefix('nutrition')->group(function () {
        Route::get('/consultations/mine', [NutritionConsultationController::class, 'mine']);
        Route::post('/consultations', [NutritionConsultationController::class, 'store']);
        Route::post('/consultations/{id}/messages', [NutritionConsultationController::class, 'storeMessage'])->whereNumber('id');
    });

    Route::middleware('role:ahli_gizi')->prefix('nutritionist')->group(function () {
        Route::get('/dashboard', [NutritionistDashboardController::class, 'index']);
        Route::get('/consultations', [NutritionConsultationController::class, 'indexNutritionist']);
    });

    Route::get('/user/stats/weekly', [\App\Http\Controllers\Api\AIAssistantController::class, 'generateWeeklyReport']);

    // FOOD & RECOGNITION
    Route::prefix('food')->group(function () {
        Route::get('/search', [\App\Http\Controllers\Api\FoodRecognitionController::class, 'search']);
        Route::get('/popular', [\App\Http\Controllers\Api\FoodRecognitionController::class, 'popular']);
        Route::post('/analyze', [\App\Http\Controllers\Api\FoodRecognitionController::class, 'analyze']);
        Route::post('/recognize-image', [\App\Http\Controllers\Api\FoodRecognitionController::class, 'recognizeImage']);
    });

    // HEALTH METRICS & SCORE
    Route::get('/health/score', [\App\Http\Controllers\Api\HealthMetricController::class, 'summary']);
    Route::post('/health/metrics', [\App\Http\Controllers\Api\HealthMetricController::class, 'store']);
    Route::get('/health/metrics/history', [\App\Http\Controllers\Api\HealthMetricController::class, 'history']);
    Route::get('/health/metrics/summary', [\App\Http\Controllers\Api\HealthMetricController::class, 'summary']);
    Route::get('/health/diary', [\App\Http\Controllers\Api\HealthMetricController::class, 'diary']);
    Route::post('/health/analyze', [\App\Http\Controllers\Api\HealthMetricController::class, 'analyze']);

    // PRODUCT REQUESTS (Crowdsourcing)
    Route::post('/product-requests', [\App\Http\Controllers\Api\ProductRequestController::class, 'store']);
    Route::post('/products/request-verification', [\App\Http\Controllers\Api\ProductRequestController::class, 'store']);

    // CONTRIBUTIONS
    Route::post('/contributions/submit', [\App\Http\Controllers\Api\ContributionController::class, 'submit']);
    Route::get('/contributions/my', [\App\Http\Controllers\Api\ContributionController::class, 'myContributions']);

    // CERTIFICATE VERIFICATION
    Route::post('/certificate/verify', [\App\Http\Controllers\Api\CertificateValidatorController::class, 'verify']);
    Route::get('/certificate/history', [\App\Http\Controllers\Api\CertificateValidatorController::class, 'history']);

    // REPORTS
    Route::post('/reports', [\App\Http\Controllers\ApiController::class, 'storeReport']);
    Route::post('/export-report', [\App\Http\Controllers\Api\ReportExportController::class, 'export']);

    // DONATIONS (campaign Midtrans)
    Route::prefix('donations')->group(function () {
        Route::get('/campaigns', [\App\Http\Controllers\Api\DonationController::class, 'campaigns']);
        Route::post('/create', [\App\Http\Controllers\Api\DonationController::class, 'create']);
        Route::get('/history', [\App\Http\Controllers\Api\DonationController::class, 'history']);
    });

    // AI ASSISTANT SUITE
    Route::prefix('ai')->group(function () {
        Route::post('/chat', [\App\Http\Controllers\Api\AIAssistantController::class, 'chat']);
        Route::post('/feedback', [\App\Http\Controllers\Api\AdminAiLogController::class, 'feedback']);
        Route::post('/analyze', [\App\Http\Controllers\Api\AIAssistantController::class, 'analyzeIngredients']);
        Route::get('/weekly-report', [\App\Http\Controllers\Api\AIAssistantController::class, 'generateWeeklyReport']);
        Route::get('/personal-risk-score', [\App\Http\Controllers\Api\AIAssistantController::class, 'getPersonalRiskScore']);
        Route::get('/daily-intake', [\App\Http\Controllers\Api\AIAssistantController::class, 'getDailyIntake']);
        Route::get('/daily-insight', [\App\Http\Controllers\Api\AIAssistantController::class, 'getPersonalHealthAdvice']);
        Route::post('/interactions', [\App\Http\Controllers\Api\DrugInteractionController::class, 'check']);
        Route::get('/drugs/search', [\App\Http\Controllers\Api\DrugInteractionController::class, 'search']);
        Route::post('/pill-identify', [\App\Http\Controllers\Api\PillIdentificationController::class, 'identify']);
        Route::get('/reminders', [\App\Http\Controllers\Api\MedicationReminderController::class, 'index']);
        Route::post('/reminders', [\App\Http\Controllers\Api\MedicationReminderController::class, 'store']);
        Route::post('/reminders/log', [\App\Http\Controllers\Api\MedicationReminderController::class, 'log']);
        Route::get('/halal-alternatives', [\App\Http\Controllers\Api\HalalAlternativeController::class, 'getAlternatives']);
        Route::post('/compare', [\App\Http\Controllers\Api\ComparisonController::class, 'compare']);
        Route::post('/bmi-advice', [\App\Http\Controllers\Api\MedicalProfileController::class, 'getAiBmiAdvice']);
    });

    // RECIPES
    Route::prefix('recipes')->group(function () {
        Route::get('/', [RecipeController::class, 'index']);
        Route::get('/{id}', [RecipeController::class, 'show']);
        Route::get('/{id}/substitution', [RecipeController::class, 'getSubstitution']);
    });

    // MEAL AI
    Route::post('/meal/analyze', [\App\Http\Controllers\Api\MealAiController::class, 'analyzeMeal']);

    // HALAL CHECK
    Route::post('/halal/check', [\App\Http\Controllers\Api\HalalCheckController::class, 'check']);

    // NUTRITION SCAN
    Route::post('/nutrition-scans', [\App\Http\Controllers\Api\NutritionScanController::class, 'scan']);

    // MEDICAL RECORDS
    Route::get('/medical-records', [\App\Http\Controllers\Api\MedicalRecordController::class, 'index']);
    Route::post('/medical-records', [\App\Http\Controllers\Api\MedicalRecordController::class, 'store']);
    Route::get('/medical-profile', [\App\Http\Controllers\Api\MedicalProfileController::class, 'show']);
    Route::post('/medical-profile', [\App\Http\Controllers\Api\MedicalProfileController::class, 'store']);
    Route::post('/ai/bmi-advice', [\App\Http\Controllers\Api\MedicalProfileController::class, 'getAiBmiAdvice']);
    Route::get('/medical-reports', [\App\Http\Controllers\Api\MedicalProfileController::class, 'getReports']);

    // FAMILY PROFILES
    Route::prefix('user/family')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FamilyController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\FamilyController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Api\FamilyController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\FamilyController::class, 'destroy']);
    });

    // EMERGENCY
    Route::post('/emergency/trigger', [\App\Http\Controllers\Api\EmergencyController::class, 'triggerEmergency']);

    // EXTRA MEDICINE ROUTES
    Route::post('/medicines/drug-food-conflict', [\App\Http\Controllers\Api\DrugInteractionController::class, 'check']);
    Route::post('/medicines/safe-schedule', [MedicineController::class, 'generateSafeSchedule']);
    Route::get('/medicines/my', [\App\Http\Controllers\Api\MedicationReminderController::class, 'index']);

    // DAILY MISSIONS

    // GAMIFICATION

    // ACHIEVEMENTS & EXPORT
    Route::get('/user/achievements', [\App\Http\Controllers\Api\ProfileFeatureController::class, 'getAchievements']);

    // ADMIN MOBILE DASHBOARD
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [MobileSyncController::class, 'getScanStats']);
        Route::get('/users', [MobileSyncController::class, 'getUserStats']);
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\AdminMonitorController::class, 'getDashboardStats']);
        Route::get('/monitor/stats', [\App\Http\Controllers\Api\AdminMonitorController::class, 'getDashboardStats']);
        Route::get('/monitor/feed', [\App\Http\Controllers\Api\AdminMonitorController::class, 'getActivityFeed']);
        Route::get('/products/pending', [\App\Http\Controllers\Api\ContributionController::class, 'pending']);
        Route::put('/products/{id}/approve', [AdminController::class, 'approveProduct']);
        Route::put('/products/{id}/reject', [AdminController::class, 'rejectProduct']);
        Route::get('/ai/logs', [\App\Http\Controllers\Api\AdminAiLogController::class, 'index']);
        Route::get('/ai/stats', [\App\Http\Controllers\Api\AdminAiLogController::class, 'stats']);
    });
});

// UTILITY
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'version' => '2.5.0']);
});

// 🩸 BLOOD DONATION (AUTHENTICATED)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blood-appointments', [\App\Http\Controllers\Api\DonorAppointmentController::class, 'store']);
    Route::get('/blood-appointments/mine', [\App\Http\Controllers\Api\DonorAppointmentController::class, 'myHistory']);
    Route::get('/blood-appointments/{id}/qr', [\App\Http\Controllers\Api\DonorAppointmentController::class, 'getQr']);
    Route::delete('/blood-appointments/{id}', [\App\Http\Controllers\Api\DonorAppointmentController::class, 'cancel']);
    Route::get('/donor-card', [\App\Http\Controllers\Api\DonorAppointmentController::class, 'donorCard']);
    Route::post('/fcm-token', [\App\Http\Controllers\Api\UserController::class, 'updateFcmToken']);
});

// 🩸 BLOOD DONATION (PUBLIC)
Route::get('/blood-events', [\App\Http\Controllers\Api\BloodEventController::class, 'index']);
Route::get('/blood-events/{id}', [\App\Http\Controllers\Api\BloodEventController::class, 'show']);
Route::get('/blood-stock', [\App\Http\Controllers\Api\BloodStockController::class, 'summary']);
Route::get('/blood-emergency', [\App\Http\Controllers\Api\EmergencyController::class, 'activeList']);

// 🩸 BLOOD DONATION (ADMIN ONLY)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('blood-events', \App\Http\Controllers\Admin\AdminBloodEventController::class);
    Route::get('appointments/{event_id}', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'byEvent']);
    Route::post('appointments/scan-qr', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'scanQr']);
    Route::post('appointments/{id}/verify', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'verify']);
    Route::apiResource('blood-stocks', \App\Http\Controllers\Admin\AdminBloodStockController::class);
    Route::get('medical-profiles', [\App\Http\Controllers\Admin\AdminMedicalProfileController::class, 'index']);
    Route::get('medical-profiles/{id}', [\App\Http\Controllers\Admin\AdminMedicalProfileController::class, 'show']);
    Route::put('medical-profiles/{id}', [\App\Http\Controllers\Admin\AdminMedicalProfileController::class, 'update']);
    Route::post('emergency-requests', [\App\Http\Controllers\Admin\EmergencyController::class, 'store']);
    Route::post('emergency-requests/{id}/notify', [\App\Http\Controllers\Admin\EmergencyController::class, 'sendNotification']);
});

// Halalytics Event System (v4.0)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events', [\App\Http\Controllers\Api\EventController::class, 'index']);
    Route::post('/events/{id}/register', [\App\Http\Controllers\Api\EventController::class, 'register']);
    Route::get('/events/my-tickets', [\App\Http\Controllers\Api\EventController::class, 'myTickets']);
});

