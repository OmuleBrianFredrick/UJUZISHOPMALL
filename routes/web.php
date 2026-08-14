<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\UserController;

Route::get('/', function () { return view('landing'); })->name('landing');
Route::post('/payments/callback/mtn', [PaymentController::class, 'callbackMtn'])->name('payments.callback.mtn');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login-enhanced'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/login/otp/request', [OtpController::class, 'requestOtp'])->name('login.otp.request');
    Route::post('/login/otp/verify', [OtpController::class, 'verify'])->name('login.otp.verify');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/shop', [StorefrontController::class, 'index'])->name('storefront.index');
    Route::get('/shop/product/{product}', [StorefrontController::class, 'show'])->name('storefront.show');
    Route::post('/shop/cart/add/{product}', [StorefrontController::class, 'addToCart'])->name('storefront.cart.add');
    Route::get('/shop/cart', [StorefrontController::class, 'cart'])->name('storefront.cart');
    Route::post('/shop/cart', [StorefrontController::class, 'updateCart'])->name('storefront.cart.update');
    Route::delete('/shop/cart/{product}', [StorefrontController::class, 'removeFromCart'])->name('storefront.cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/pay', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/orders/{order}/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    Route::get('/sell', [SellerController::class, 'apply'])->name('seller.apply');
    Route::post('/sell', [SellerController::class, 'store'])->name('seller.apply.store');
    Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/analytics', [ProductController::class, 'analytics'])->name('products.analytics');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{product}/stock-in', [ProductController::class, 'stockIn'])->name('products.stockIn');
    Route::post('/products/{product}/stock-in', [ProductController::class, 'processStockIn'])->name('products.stockIn.submit');
    Route::get('/products/{product}/stock-out', [ProductController::class, 'stockOut'])->name('products.stockOut');
    Route::post('/products/{product}/stock-out', [ProductController::class, 'processStockOut'])->name('products.stockOut.submit');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});
