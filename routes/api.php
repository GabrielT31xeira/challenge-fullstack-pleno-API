<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProfileController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);
    });
});

Route::prefix('v1/products')->group(function () {

    Route::get('/', [\App\Http\Controllers\api\ProductController::class, 'index']);
    Route::get('{id}', [\App\Http\Controllers\api\ProductController::class, 'show']);

    Route::middleware(['admin'])->group(function () {
        Route::post('/', [\App\Http\Controllers\api\ProductController::class, 'store']);
        Route::put('{id}', [\App\Http\Controllers\api\ProductController::class, 'update']);
        Route::delete('{id}', [\App\Http\Controllers\api\ProductController::class, 'destroy']);
    });
});

