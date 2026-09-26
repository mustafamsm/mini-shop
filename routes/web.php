<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('shop.product.show');

Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
});
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/images', [ProductImageController::class, 'store'])->name('images.store');
    Route::delete('products/{product}/images/{mediaId}', [ProductImageController::class, 'destroy'])->name('images.destroy');
    Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
    Route::patch('variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
    Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('users',UserController::class)->only(['index','update']);

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::patch('roles/{role}', [RoleController::class, 'update'])->name('roles.update');

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

});

Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

require __DIR__.'/settings.php';
