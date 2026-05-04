<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserCartController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/** Brauzer sahifalari: session + auth uchun web orali (faqat GET HTML; JSON API api.php'da qoladi) */
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

// Savatcha sahifasi (mahsulotlar localStorage'da, sahifa shunchaki shablon)
Route::view('/cart', 'cart.index')->name('cart.index');

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/cart/data', [UserCartController::class, 'show']);
    Route::put('/cart/data', [UserCartController::class, 'sync']);
});

Route::get('/checkout', [CheckoutController::class, 'show'])->middleware('auth')->name('checkout');
Route::post('/checkout/order', [CheckoutController::class, 'storeOrder'])->middleware('auth')->name('checkout.order');
Route::get('/checkout/thanks', [CheckoutController::class, 'thanks'])->middleware('auth')->name('checkout.thanks');
Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->middleware('auth')->name('checkout.payment');
