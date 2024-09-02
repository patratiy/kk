<?php

use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/image', [ProductController::class, 'getImage']);
Route::get('/test', [ProductController::class, 'test']);
