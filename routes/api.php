<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.forgot-password');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('api.reset-password');

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.me');
    Route::put('/auth/profile', [AuthController::class, 'updateProfile'])->name('api.update-profile');
    Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('api.change-password');
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
    
    // Stock Management
    Route::prefix('stock')->group(function () {
        Route::post('/in', [StockController::class, 'stockIn'])->name('api.stock.in');
        Route::post('/out', [StockController::class, 'stockOut'])->name('api.stock.out');
        Route::get('/history', [StockController::class, 'history'])->name('api.stock.history');
        Route::get('/movements', [StockController::class, 'movements'])->name('api.stock.movements');
    });
    
    // User Management
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('api.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('api.users.show');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('api.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('api.users.destroy');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('api.users.toggle-status');
    });
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
    
    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('api.suppliers.index');
    
    // Medicines
    Route::prefix('medicines')->group(function () {
        Route::get('/search', [MedicineController::class, 'search'])->name('api.medicines.search');
        Route::get('/low-stock', [MedicineController::class, 'lowStock'])->name('api.medicines.low-stock');
        Route::get('/expiring', [MedicineController::class, 'expiring'])->name('api.medicines.expiring');
        Route::get('/', [MedicineController::class, 'index'])->name('api.medicines.index');
        Route::post('/', [MedicineController::class, 'store'])->name('api.medicines.store');
        Route::get('/{medicine}', [MedicineController::class, 'show'])->name('api.medicines.show');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('api.medicines.update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('api.medicines.destroy');
    });
    
    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('api.sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('api.sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('api.sales.show');
    Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('api.sales.update');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('api.sales.destroy');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('api.sales.invoice');
    
    // Chatbot
    Route::post('/chatbot/process', [ChatbotController::class, 'process'])->name('api.chatbot.process');
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('api.reports.sales');
    Route::get('/reports/stock-alert', [ReportController::class, 'stockAlert'])->name('api.reports.stock-alert');
    Route::get('/reports/expiry', [ReportController::class, 'expiry'])->name('api.reports.expiry');
});
