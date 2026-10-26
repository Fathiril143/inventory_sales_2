<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerStorefrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CustomerStorefrontController::class, 'index'])->name('storefront.index');
Route::get('/pesan', [CustomerStorefrontController::class, 'create'])->name('storefront.create');
Route::post('/pesan', [CustomerStorefrontController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('storefront.store');
Route::get('/pesanan-selesai', [CustomerStorefrontController::class, 'thankYou'])->name('storefront.thank-you');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

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
    });

    Route::resource('purchases', PurchaseController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::resource('sales', SaleController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
