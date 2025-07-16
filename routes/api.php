<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//route login
Route::post('/login', [LoginController::class, 'login']);

//route data products
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{product}', [ProductsController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/users', [LoginController::class, 'index']);
    Route::post('/users', [LoginController::class, 'store']);
    Route::put('/users/{user}', [LoginController::class, 'update']);
    Route::delete('/users/{user}', [LoginController::class, 'destroy']);

    Route::post('/products', [ProductsController::class, 'store']);
    Route::put('/products/{product}', [ProductsController::class, 'update']);
    Route::delete('/products/{product}', [ProductsController::class, 'destroy']);
});
