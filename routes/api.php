<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UpdateProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json(['message' => 'API route not found.'], 404);
});
//route login
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);


//route data products
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{product}', [ProductsController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::put('/user', [UpdateProfileController::class, 'update']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/requests', [ProductRequestController::class, 'index']);
    Route::get('/requests/products', [ProductRequestController::class, 'getProductsFromRequests']);
    Route::post('/requestsmodel', action: [ProductRequestController::class, 'store']);
    Route::post('/checkout', [CheckoutController::class,'store']);
Route::post('/payment/snap-token', [PaymentController::class, 'getSnapToken']);

});

Route::post('/register', [RegisterController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verifyEmail'])
    ->middleware(['signed'])->name('verification.verify');


Route::post('/payment/callback', [PaymentController::class, 'handle']);
