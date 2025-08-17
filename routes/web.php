<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProductController;

Route::get('/{username}', [FrontendController::class, 'index'])->name('index');

Route::get('/{username}/find-product', [ProductController::class, 'find'])->name('product.find');
Route::get('/{username}/find-product/results', [ProductController::class, 'findResults'])->name('product.find-results');
Route::get('/{username}/product/{id}', [ProductController::class, 'show'])->name('product.show');

// routes/web.php
Route::get('/{username}/categories', [ProductController::class, 'categories'])->name('product.categories');
Route::get('/{username}/favorites', [ProductController::class, 'favorites'])->name('product.favorites');
Route::get('/{username}/recommendations', [ProductController::class, 'recommendations'])->name('product.recommendations');
