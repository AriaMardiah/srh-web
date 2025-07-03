<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/users', [LoginController::class, 'index']);
    Route::post('/users', [LoginController::class, 'store']);
    Route::put('/users/{user}', [LoginController::class, 'update']);
    Route::delete('/users/{user}', [LoginController::class, 'destroy']);
});
