<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/products/next-code', [ProductController::class, 'getNextCode']);
Route::resource('products', ProductController::class);
