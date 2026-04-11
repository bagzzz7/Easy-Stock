<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/features', [LandingController::class, 'features'])->name('landing.features');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
Route::get('/demo', [LandingController::class, 'demo'])->name('landing.demo');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Email Verification
    Route::get('email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
    
    // Password Confirmation
    Route::get('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('upload-photo');
        Route::delete('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
    });
    
    // Medicines - Custom routes FIRST
    Route::get('/medicines/low-stock', [MedicineController::class, 'lowStock'])->name('medicines.low-stock');
    Route::get('/medicines/expiring', [MedicineController::class, 'expiring'])->name('medicines.expiring');
    Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/medicines/export', [MedicineController::class, 'export'])->name('medicines.export');
    
    // Resource route
    Route::resource('medicines', MedicineController::class);
    Route::post('/medicines/{medicine}/restock', [MedicineController::class, 'restock'])->name('medicines.restock');
    
    // Sales
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/stock-alert', [ReportController::class, 'stockAlert'])->name('stock-alert');
        Route::get('/expiry', [ReportController::class, 'expiry'])->name('expiry');
    });
    
    // Suppliers (Admin & Staff only)
    Route::middleware(['role:administrator,staff,pharmacist'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
        Route::post('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
    });
    
    // Categories (Admin & Staff only)
    Route::middleware(['role:administrator,staff'])->group(function () {
        Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    });
    
    // User Management (Admin & Staff only)
    Route::middleware(['role:administrator,staff'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
    
    // Chatbot
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/process', [ChatbotController::class, 'process'])->name('process');
        Route::get('/history', [ChatbotController::class, 'getHistory'])->name('history');
    });
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/',     [StockMovementController::class, 'index'])->name('history');
    Route::get('/in',   [StockMovementController::class, 'stockInForm'])->name('in');
    Route::post('/in',  [StockMovementController::class, 'stockIn'])->name('in.store');
    Route::get('/out',  [StockMovementController::class, 'stockOutForm'])->name('out');
    Route::post('/out', [StockMovementController::class, 'stockOut'])->name('out.store');
});
   
});

