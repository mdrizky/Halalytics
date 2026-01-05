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

// ================== AUTH ==================

// Login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/actionLogin', [LoginController::class, 'login'])->name('actionLogin');

// Register
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register.form');
Route::post('/registeraction', [RegisterController::class, 'registeraction'])->name('registeraction');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ================== DASHBOARD ADMIN ==================

// Halaman Home per Role
Route::get('/admin', [DashboardController::class, 'index'])
    ->name('admin.home')
    ->middleware('auth');

Route::get('/user', function () {
    return view('user_home'); 
})->name('user.home')->middleware('auth'); 


// ================== ADMIN ==================
Route::middleware('auth')->group(function () {

    // Users
    Route::get('/admin/user', [AdminUserController::class, 'admin_user'])->name('admin.user');
    Route::get('/admin/user/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.user_edit');
    Route::put('/admin/user/{id}', [AdminUserController::class, 'update'])->name('admin.user_update');
    Route::delete('/admin/user/{id}', [AdminUserController::class, 'hapus'])->name('admin.user_hapus');
    Route::patch('/admin/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('admin.user_toggle');

    // Products
    Route::get('/admin/product', [AdminProductController::class, 'admin_product'])->name('admin_product');
    Route::get('/admin/product/create', [AdminProductController::class, 'create'])->name('admin.product_tambah');
    Route::post('/admin/product/store', [AdminProductController::class, 'store'])->name('admin_product.store');
    Route::get('/admin/product/{id}/edit', [AdminProductController::class, 'edit'])->name('admin_product.edit');
    Route::post('/admin/product/{id}/update', [AdminProductController::class, 'update'])->name('admin_product.update');
    Route::delete('/admin/product/{id}', [AdminProductController::class, 'destroy'])->name('admin_product.destroy');

    // 🔎 Search Produk by Barcode (lokal + internasional)
    Route::get('/admin/product/search/{barcode}', [AdminProductController::class, 'searchByBarcode'])->name('admin_product.search');

    // Scan CRUD
    Route::prefix('admin')->group(function () {
        Route::get('/scan', [AdminScanController::class, 'index'])->name('scan.index');
        Route::get('/scan/{id}', [AdminScanController::class, 'show'])->name('scan.show');
        Route::get('/scan/{id}/edit', [AdminScanController::class, 'edit'])->name('scan.edit');
        Route::put('/scan/{id}', [AdminScanController::class, 'update'])->name('scan.update');
        Route::delete('/scan/{id}', [AdminScanController::class, 'destroy'])->name('scan.destroy');
    });

    // Kategori
    Route::get('/admin/kategori', [AdminKategoriController::class, 'index'])->name('admin.kategori');

    // Reports
    Route::get('/admin/reports', [AdminReportController::class, 'admin_report'])->name('admin_report');
    Route::put('/admin/reports/{id}/status', [AdminReportController::class, 'update_status'])->name('admin_report.update_status');
    Route::delete('/admin/reports/{id}', [AdminReportController::class, 'destroy'])->name('admin_report.destroy');


});
