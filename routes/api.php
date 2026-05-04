<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'index'])->name('search');
// `{book}` dan oldin yozilishi kerak bo‘lmagan id sifatida yutilmasligi uchun:
Route::get('/books/stock-check', [BookController::class, 'getBookStock']);
Route::apiResource('books', BookController::class)->except(['index']);
Route::apiResource('authors', AuthorController::class)->except(['show']);
