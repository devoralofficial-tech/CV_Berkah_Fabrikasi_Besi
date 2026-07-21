<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Checkout — rate limited to prevent spam
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')
    ->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Settings & Profile
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('settings.activity-log');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Products
    Route::get('/products/child-categories/{parentId}', [AdminProductController::class, 'getChildCategories'])
        ->name('products.child-categories');
    Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', AdminProductController::class);

    // Warehouse (Gudang)
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/stock-in', [WarehouseController::class, 'stockInCreate'])->name('stock-in');
        Route::post('/stock-in', [WarehouseController::class, 'stockInStore'])->name('stock-in.store');
        Route::get('/stock-out', [WarehouseController::class, 'stockOutCreate'])->name('stock-out');
        Route::post('/stock-out', [WarehouseController::class, 'stockOutStore'])->name('stock-out.store');
        Route::get('/opname', [WarehouseController::class, 'opnameIndex'])->name('opname-index');
        Route::get('/opname/create', [WarehouseController::class, 'opnameCreate'])->name('opname-create');
        Route::post('/opname', [WarehouseController::class, 'opnameStore'])->name('opname-store');
        Route::get('/opname/{opname}', [WarehouseController::class, 'opnameShow'])->name('opname-show');
        Route::get('/stock-card/{product}', [WarehouseController::class, 'stockCard'])->name('stock-card');
        Route::get('/low-stock', [WarehouseController::class, 'lowStock'])->name('low-stock');
    });

    // Sales / Kasir
    Route::get('/sales/search-products', [SaleController::class, 'searchProducts'])->name('sales.search-products');
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
    Route::resource('sales', SaleController::class)->only(['create', 'store', 'index', 'show']);

    // Online Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    });
    // Chatbot
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ChatbotController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ChatbotController::class, 'store'])->name('store');
        Route::put('/{faq}', [\App\Http\Controllers\Admin\ChatbotController::class, 'update'])->name('update');
        Route::delete('/{faq}', [\App\Http\Controllers\Admin\ChatbotController::class, 'destroy'])->name('destroy');
        Route::get('/unanswered', [\App\Http\Controllers\Admin\ChatbotController::class, 'unanswered'])->name('unanswered');
    });
});

// Chatbot API
Route::get('/chatbot/faqs', [\App\Http\Controllers\ChatbotController::class, 'getFaqs']);
Route::post('/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask']);

// Breeze auth routes (login, logout, password reset — NO register)
require __DIR__ . '/auth.php';
