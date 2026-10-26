<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerStorefrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentChannelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Customer Storefront
Route::get('/', [CustomerStorefrontController::class, 'index'])->name('storefront.index');
Route::get('/pesan', [CustomerStorefrontController::class, 'create'])->name('storefront.create');
Route::post('/pesan', [CustomerStorefrontController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('storefront.store');
Route::get('/pesanan-selesai', [CustomerStorefrontController::class, 'thankYou'])->name('storefront.thank-you');
Route::get('/customer-access', [AuthController::class, 'customerAccess'])->name('customer.access');

// Public Payment Gateway (Midtrans/Xendit Mock & Demo Simulation)
Route::prefix('payment')->group(function () {
    Route::get('/{reference}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/{reference}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
    Route::post('/{reference}/simulate-paid', [PaymentController::class, 'simulatePaid'])->name('payment.simulate-paid');
    Route::post('/{reference}/simulate-expired', [PaymentController::class, 'simulateExpired'])->name('payment.simulate-expired');
    Route::get('/{reference}/status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
    Route::get('/{reference}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Back-Office: Admin & Staff
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::resource('suppliers', SupplierController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

        // Admin Payment Channels Management
        Route::get('/admin/payment-channels', [PaymentChannelController::class, 'index'])->name('payment-channels.index');
        Route::post('/admin/payment-channels/{channel}/toggle', [PaymentChannelController::class, 'toggle'])->name('payment-channels.toggle');
        Route::get('/admin/payment-channels/{channel}/edit', [PaymentChannelController::class, 'edit'])->name('payment-channels.edit');
        Route::put('/admin/payment-channels/{channel}', [PaymentChannelController::class, 'update'])->name('payment-channels.update');

        // Admin Financial Payment Reports
        Route::get('/admin/payment-reports', [PaymentReportController::class, 'index'])->name('payment-reports.index');
    });

    Route::resource('purchases', PurchaseController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::resource('sales', SaleController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Staff Payment Verification & Warehouse Fulfillment
    Route::post('/sales/{sale}/verify-payment', [SaleController::class, 'verifyPayment'])->name('sales.verify-payment');
    Route::post('/sales/{sale}/fulfillment', [SaleController::class, 'updateFulfillment'])->name('sales.update-fulfillment');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
