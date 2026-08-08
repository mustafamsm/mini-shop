<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;




Route::get('/', [ProductController::class, 'index'])->name('shop.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('shop.product.show');

Route::post('/cart/items', [CartController::class, 'store']);
Route::put('/cart/items/{cartItem}', [CartController::class, 'update']);
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy']);

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index']);
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});

Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

require __DIR__ . '/settings.php';
