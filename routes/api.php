<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::apiResource('books', BookController::class)->except(['index']);
Route::apiResource('authors', AuthorController::class)->except(['show']);
