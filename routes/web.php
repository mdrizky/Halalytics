<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminScanController;
use App\Http\Controllers\AdminKategoriController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminPengaturanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\HalalProductController;
use App\Http\Controllers\AdminApiController;
use App\Http\Controllers\Api\OCRController;
use App\Http\Controllers\Admin\Controller as WebAdminController;
use App\Http\Controllers\Api\AdminController as ApiAdminController;
use App\Http\Controllers\OCRController as WebOCRController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\OpenFoodFactsAdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\UserPortalController;
use App\Http\Controllers\Admin\IngredientManagementController;

// ================== PROMO WEBSITE ==================

Route::get('/', [App\Http\Controllers\Promo\PageController::class, 'home'])->name('home');
Route::get('/features', [App\Http\Controllers\Promo\PageController::class, 'features'])->name('features');
Route::get('/about', [App\Http\Controllers\Promo\PageController::class, 'about'])->name('about');
Route::get('/download', [App\Http\Controllers\Promo\PageController::class, 'download'])->name('download');
Route::get('/privacy', [App\Http\Controllers\Promo\PageController::class, 'privacy'])->name('privacy');
Route::get('/blog', [App\Http\Controllers\Promo\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [App\Http\Controllers\Promo\BlogController::class, 'show'])->name('blog.show');
Route::post('/contact', [App\Http\Controllers\Promo\ContactController::class, 'send'])->name('contact.send');

// ================== AUTH ==================

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/actionLogin', [LoginController::class, 'login'])->name('actionLogin');

// Register
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register.form');
Route::post('/registeraction', [RegisterController::class, 'registeraction'])->name('registeraction');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ================== DASHBOARD ADMIN ==================

// Product Requests
Route::get('/admin/requests', [App\Http\Controllers\Admin\AdminRequestController::class, 'index'])->name('admin.requests.index');
Route::post('/admin/requests/{id}/approve', [App\Http\Controllers\Admin\AdminRequestController::class, 'approve'])->name('admin.requests.approve');
Route::post('/admin/requests/{id}/reject', [App\Http\Controllers\Admin\AdminRequestController::class, 'reject'])->name('admin.requests.reject');

// Dashboard API routes
Route::prefix('admin/dashboard')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/monitor/stats', [DashboardController::class, 'monitorStats'])->name('admin.dashboard.monitor.stats');
    Route::get('/monitor/feed', [DashboardController::class, 'monitorFeed'])->name('admin.dashboard.monitor.feed');
    Route::get('/health', [DashboardController::class, 'systemHealth']);
    Route::get('/trends', [DashboardController::class, 'getTrendIndicators']);
    Route::get('/export', [DashboardController::class, 'exportDashboard'])->name('admin.dashboard.export');
});

// Global Search
Route::get('/admin/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])
    ->name('admin.global.search')
    ->middleware(['auth', 'role:admin']);

// Admin Notifications API
Route::prefix('admin/notifications-api')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index']);
    Route::get('/unread-count', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'unreadCount']);
    Route::post('/{id}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead']);
    Route::post('/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead']);
});

Route::get('/user', function () {
    return view('user_home'); 
})->name('user.home')->middleware('auth');

// User Portal Routes
Route::middleware('auth')->group(function () {
    Route::get('/my-scans', [UserPortalController::class, 'myScans'])->name('user.scans');
    Route::get('/products', [UserPortalController::class, 'products'])->name('user.products');
    Route::get('/reports', [UserPortalController::class, 'reports'])->name('user.reports');
    Route::post('/reports', [UserPortalController::class, 'storeReport'])->name('user.reports.store');
    Route::get('/scan/barcode', [UserPortalController::class, 'scanner'])->name('user.scanner');
});
// ================== ADMIN ==================

// Admin dashboard
Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware(['auth', 'role:admin']);
Route::get('/admin/stats', [DashboardController::class, 'getStats'])->name('admin.dashboard.stats')->middleware(['auth', 'role:admin']);

// ================== OCR MANAGEMENT ==================

Route::prefix('admin/ocr')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [WebOCRController::class, 'index'])->name('admin.ocr.index');
    Route::get('/pending', [WebOCRController::class, 'getPendingProducts'])->name('admin.ocr.pending');
    Route::get('/approved', [WebOCRController::class, 'getApprovedProducts'])->name('admin.ocr.approved');
    Route::get('/rejected', [WebOCRController::class, 'getRejectedProducts'])->name('admin.ocr.rejected');
    Route::get('/statistics', [WebOCRController::class, 'statistics'])->name('admin.ocr.statistics');
    Route::get('/product/{id}', [WebOCRController::class, 'show'])->name('admin.ocr.show');
    Route::post('/approve/{id}', [WebOCRController::class, 'approve'])->name('admin.ocr.approve');
    Route::post('/reject/{id}', [WebOCRController::class, 'reject'])->name('admin.ocr.reject');
    Route::post('/bulk-approve', [WebOCRController::class, 'bulkApprove'])->name('admin.ocr.bulkApprove');
    Route::post('/bulk-reject', [WebOCRController::class, 'bulkReject'])->name('admin.ocr.bulkReject');
    Route::get('/export', [WebOCRController::class, 'export'])->name('admin.ocr.export');
    Route::post('/upload-web', [WebOCRController::class, 'uploadImage'])->name('admin.ocr.upload_web');
});

// Admin routes group
Route::middleware('auth')->group(function () {
    
    // Promo Manager (Admin)
    Route::prefix('admin/promo')->name('admin.promo.')->middleware('role:admin')->group(function () {
        // Blog
        Route::get('/blog', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'store'])->name('blog.store');
        Route::get('/blog/{id}/edit', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'edit'])->name('blog.edit');
        Route::put('/blog/{id}', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'update'])->name('blog.update');
        Route::post('/blog/{id}/toggle', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'toggle'])->name('blog.toggle');
        Route::delete('/blog/{id}', [App\Http\Controllers\Admin\Promo\PromoBlogController::class, 'destroy'])->name('blog.destroy');
        
        // Settings
        Route::get('/settings', [App\Http\Controllers\Admin\Promo\PromoSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\Promo\PromoSettingController::class, 'update'])->name('settings.update');
        
        // Messages
        Route::get('/messages', [App\Http\Controllers\Admin\Promo\PromoMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{id}', [App\Http\Controllers\Admin\Promo\PromoMessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{id}', [App\Http\Controllers\Admin\Promo\PromoMessageController::class, 'destroy'])->name('messages.destroy');
    });
    
    // Users
    Route::get('/admin/user', [AdminUserController::class, 'admin_user'])->name('admin.user.index')->middleware('role:admin');
    Route::get('/admin/user/create', [AdminUserController::class, 'create'])->name('admin.user.create')->middleware('role:admin');
    Route::post('/admin/user/store', [AdminUserController::class, 'store'])->name('admin.user.store')->middleware('role:admin');
    Route::get('/admin/user/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.user.edit')->middleware('role:admin');
    Route::put('/admin/user/{id}', [AdminUserController::class, 'update'])->name('admin.user.update')->middleware('role:admin');
    Route::delete('/admin/user/{id}', [AdminUserController::class, 'hapus'])->name('admin.user.destroy')->middleware('role:admin');
    Route::patch('/admin/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('admin.user.toggle')->middleware('role:admin');
    Route::patch('/admin/users/{id}/role', [AdminUserController::class, 'changeRole'])->name('admin.user.role')->middleware('role:admin');
    
    // Alias for old user route if any
    Route::get('/admin/user/list', [AdminUserController::class, 'admin_user'])->name('admin.user')->middleware('role:admin');

    // Products
    Route::get('/admin/product', [AdminProductController::class, 'admin_product'])->name('admin.product.index')->middleware('role:admin');
    Route::get('/admin/product/create', [AdminProductController::class, 'create'])->name('admin.product.create')->middleware('role:admin');
    Route::get('/admin/product/ocr', [AdminProductController::class, 'ocrScanner'])->name('admin.product.ocr')->middleware('role:admin');
    Route::post('/admin/product/store', [AdminProductController::class, 'store'])->name('admin.product.store')->middleware('role:admin');
    Route::get('/admin/product/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.product.edit')->middleware('role:admin');
    Route::post('/admin/product/{id}/update', [AdminProductController::class, 'update'])->name('admin.product.update')->middleware('role:admin');
    Route::delete('/admin/product/{id}', [AdminProductController::class, 'destroy'])->name('admin.product.destroy')->middleware('role:admin');
    
    // Alias for old product routes
    Route::get('/admin/product/all', [AdminProductController::class, 'admin_product'])->name('admin_product_new')->middleware('role:admin');
    Route::get('/admin/product/tambah', [AdminProductController::class, 'create'])->name('admin.product.tambah')->middleware('role:admin');

    // Search Produk by Barcode
    Route::get('/admin/product/search/{barcode}', [AdminProductController::class, 'searchByBarcode'])->name('admin.product.search')->middleware('role:admin');
    Route::patch('/admin/product/{id}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('admin.product.toggle_active')->middleware('role:admin');
    Route::post('/admin/product/batch-ai-verify', [AdminProductController::class, 'batchAiVerify'])->name('admin.product.batch_ai_verify')->middleware('role:admin');
    Route::post('/admin/product/apply-batch-ai-verify', [AdminProductController::class, 'applyBatchAiVerify'])->name('admin.product.apply_batch_ai_verify')->middleware('role:admin');

    // Scan CRUD
    Route::get('/admin/scan', [AdminScanController::class, 'index'])->name('admin.scan.index')->middleware('role:admin');
    Route::get('/admin/scan/create', [AdminScanController::class, 'create'])->name('admin.scan.create')->middleware('role:admin');
    Route::post('/admin/scan', [AdminScanController::class, 'store'])->name('admin.scan.store')->middleware('role:admin');
    Route::get('/admin/scan/{id}', [AdminScanController::class, 'show'])->name('admin.scan.show')->middleware('role:admin');
    Route::get('/admin/scan/{id}/edit', [AdminScanController::class, 'edit'])->name('admin.scan.edit')->middleware('role:admin');
    Route::put('/admin/scan/{id}', [AdminScanController::class, 'update'])->name('admin.scan.update')->middleware('role:admin');
    Route::delete('/admin/scan/{id}', [AdminScanController::class, 'destroy'])->name('admin.scan.destroy')->middleware('role:admin');
    Route::get('/admin/scan-export-pdf', [AdminScanController::class, 'exportPdf'])->name('admin.scan.export_pdf')->middleware('role:admin');

    // Categories
    Route::get('/admin/kategori', [AdminKategoriController::class, 'index'])->name('admin.kategori.index')->middleware('role:admin');
    Route::get('/admin/kategori/all', [AdminKategoriController::class, 'index'])->name('admin.kategori')->middleware('role:admin');
    Route::get('/admin/kategori/create', [AdminKategoriController::class, 'create'])->name('admin.kategori.create')->middleware('role:admin');
    Route::post('/admin/kategori', [AdminKategoriController::class, 'store'])->name('admin.kategori.store')->middleware('role:admin');
    Route::get('/admin/kategori/{id}/edit', [AdminKategoriController::class, 'edit'])->name('admin.kategori.edit')->middleware('role:admin');
    Route::put('/admin/kategori/{id}', [AdminKategoriController::class, 'update'])->name('admin.kategori.update')->middleware('role:admin');
    Route::delete('/admin/kategori/{id}', [AdminKategoriController::class, 'destroy'])->name('admin.kategori.destroy')->middleware('role:admin');

    // Reports
    Route::get('/admin/reports', [AdminReportController::class, 'admin_report'])->name('admin.report.index')->middleware('role:admin');
    Route::put('/admin/reports/{id}/status', [AdminReportController::class, 'update_status'])->name('admin.report.update_status')->middleware('role:admin');
    Route::post('/admin/reports/{id}/resolve-forgery', [AdminReportController::class, 'resolveForgery'])->name('admin.report.resolve_forgery')->middleware('role:admin');
    Route::delete('/admin/reports/{id}', [AdminReportController::class, 'destroy'])->name('admin.report.destroy')->middleware('role:admin');
    Route::get('/admin/reports-export-pdf', [AdminReportController::class, 'exportPdf'])->name('admin.report.export_pdf')->middleware('role:admin');
    Route::post('/admin/reports/batch-verify', [AdminReportController::class, 'batchVerify'])->name('admin.report.batch_verify')->middleware('role:admin');

    // Halal Products
    Route::get('/admin/halal-products', [HalalProductController::class, 'index'])->name('halal-products.index')->middleware('role:admin');
    Route::post('/admin/halal-products', [HalalProductController::class, 'store'])->name('halal-products.store')->middleware('role:admin');
    Route::get('/admin/halal-products/create', [HalalProductController::class, 'create'])->name('halal-products.create')->middleware('role:admin');
    Route::get('/admin/halal-products/{id}', [HalalProductController::class, 'show'])->name('halal-products.show')->middleware('role:admin');
    Route::get('/admin/halal-products/{id}/edit', [HalalProductController::class, 'edit'])->name('halal-products.edit')->middleware('role:admin');
    Route::put('/admin/halal-products/{id}', [HalalProductController::class, 'update'])->name('halal-products.update')->middleware('role:admin');
    Route::delete('/admin/halal-products/{id}', [HalalProductController::class, 'destroy'])->name('halal-products.destroy')->middleware('role:admin');
    Route::post('/admin/halal-products/search', [HalalProductController::class, 'search'])->name('halal-products.search')->middleware('role:admin');
    Route::post('/admin/halal-products/{id}/verify', [HalalProductController::class, 'verify'])->name('halal-products.verify')->middleware('role:admin');

    // Banners
    Route::get('/admin/banner', [BannerController::class, 'index'])->name('admin.banner')->middleware('role:admin');
    Route::post('/admin/banner/store', [BannerController::class, 'store'])->name('admin.banner.store')->middleware('role:admin');
    Route::put('/admin/banner/{banner}', [BannerController::class, 'update'])->name('admin.banner.update')->middleware('role:admin');
    Route::delete('/admin/banner/{banner}', [BannerController::class, 'destroy'])->name('admin.banner.destroy')->middleware('role:admin');

    // Forbidden Ingredients Management
    Route::get('/admin/forbidden', [\App\Http\Controllers\AdminForbiddenController::class, 'index'])->name('admin.forbidden.index')->middleware('role:admin');
    Route::post('/admin/forbidden', [\App\Http\Controllers\AdminForbiddenController::class, 'store'])->name('admin.forbidden.store')->middleware('role:admin');
    Route::put('/admin/forbidden/{id}', [\App\Http\Controllers\AdminForbiddenController::class, 'update'])->name('admin.forbidden.update')->middleware('role:admin');
    Route::delete('/admin/forbidden/{id}', [\App\Http\Controllers\AdminForbiddenController::class, 'destroy'])->name('admin.forbidden.destroy')->middleware('role:admin');

    // Notifications
    Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class)->names('admin.notifications');
    
    // UMKM
    Route::resource('umkm', \App\Http\Controllers\Admin\UmkmProductController::class)->names('admin.umkm');
    Route::get('umkm/{id}/download-qr', [\App\Http\Controllers\Admin\UmkmProductController::class, 'downloadQR'])->name('admin.umkm.download-qr');

    // Ingredients Encyclopedia
    Route::get('/admin/ingredients', [IngredientManagementController::class, 'index'])->name('admin.ingredients.index')->middleware('role:admin');
    Route::get('/admin/ingredients/create', [IngredientManagementController::class, 'create'])->name('admin.ingredients.create')->middleware('role:admin');
    Route::post('/admin/ingredients', [IngredientManagementController::class, 'store'])->name('admin.ingredients.store')->middleware('role:admin');
    Route::get('/admin/ingredients/{id}/edit', [IngredientManagementController::class, 'edit'])->name('admin.ingredients.edit')->middleware('role:admin');
    Route::put('/admin/ingredients/{id}', [IngredientManagementController::class, 'update'])->name('admin.ingredients.update')->middleware('role:admin');
    Route::delete('/admin/ingredients/{id}', [IngredientManagementController::class, 'destroy'])->name('admin.ingredients.destroy')->middleware('role:admin');

    // Street Foods
    Route::get('/admin/street-foods', [\App\Http\Controllers\Admin\StreetFoodController::class, 'index'])->name('admin.street-foods.index')->middleware('role:admin');
    Route::get('/admin/street-foods/create', [\App\Http\Controllers\Admin\StreetFoodController::class, 'create'])->name('admin.street-foods.create')->middleware('role:admin');
    Route::post('/admin/street-foods', [\App\Http\Controllers\Admin\StreetFoodController::class, 'store'])->name('admin.street-foods.store')->middleware('role:admin');
    Route::get('/admin/street-foods/{streetFood}/edit', [\App\Http\Controllers\Admin\StreetFoodController::class, 'edit'])->name('admin.street-foods.edit')->middleware('role:admin');
    Route::put('/admin/street-foods/{streetFood}', [\App\Http\Controllers\Admin\StreetFoodController::class, 'update'])->name('admin.street-foods.update')->middleware('role:admin');
    Route::delete('/admin/street-foods/{streetFood}', [\App\Http\Controllers\Admin\StreetFoodController::class, 'destroy'])->name('admin.street-foods.destroy')->middleware('role:admin');
    
    // Variants
    Route::get('/admin/street-foods/{streetFood}/variants', [\App\Http\Controllers\Admin\StreetFoodController::class, 'variants'])->name('admin.street-foods.variants')->middleware('role:admin');
    Route::post('/admin/street-foods/{streetFood}/variants', [\App\Http\Controllers\Admin\StreetFoodController::class, 'storeVariant'])->name('admin.street-foods.variants.store')->middleware('role:admin');
    Route::delete('/admin/variants/{variant}', [\App\Http\Controllers\Admin\StreetFoodController::class, 'destroyVariant'])->name('admin.street-foods.variants.destroy')->middleware('role:admin');

    // User Management
    Route::prefix('admin/users')->middleware('role:admin')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/{id}', [UserManagementController::class, 'show'])->name('admin.users.show');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/{id}/scan-history', [UserManagementController::class, 'scanHistory'])->name('admin.users.scan-history');
        Route::get('/{id}/export', [UserManagementController::class, 'export'])->name('admin.users.export');
        Route::get('/dashboard', [UserManagementController::class, 'dashboard'])->name('admin.users.dashboard');
    });

    // OpenFoodFacts Management
    Route::prefix('admin/openfoodfacts')->middleware('role:admin')->group(function () {
        Route::get('/', [OpenFoodFactsAdminController::class, 'index'])->name('admin.products.off.index');
        Route::get('/search', [OpenFoodFactsAdminController::class, 'search'])->name('admin.products.off.search');
        Route::get('/preview/{offId}', [OpenFoodFactsAdminController::class, 'preview'])->name('admin.products.off.preview');
        Route::post('/import/{offId}', [OpenFoodFactsAdminController::class, 'import'])->name('admin.products.off.import');
        Route::get('/auto-imported', [OpenFoodFactsAdminController::class, 'autoImported'])->name('admin.products.off.auto-imported');
    });

    // BPOM Verification Management
    Route::prefix('admin/bpom')->middleware('role:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BpomAdminController::class, 'index'])->name('admin.bpom.index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\BpomAdminController::class, 'show'])->name('admin.bpom.show');
        Route::post('/{id}/verify', [\App\Http\Controllers\Admin\BpomAdminController::class, 'verify'])->name('admin.bpom.verify');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BpomAdminController::class, 'destroy'])->name('admin.bpom.destroy');
    });

});

// NOTE: API routes are already loaded via RouteServiceProvider with 'api' middleware
// Do NOT require api.php here as it will duplicate routes under 'web' middleware
