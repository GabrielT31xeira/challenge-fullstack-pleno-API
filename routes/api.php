<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\OrderController;

Route::prefix('v1')->group(function () {

    // --- Auth ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);

        // --- Categories ---
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

        // --- Products ---
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);

            Route::middleware('admin')->group(function () {
                Route::post('/', [ProductController::class, 'store']);
                Route::put('/{id}', [ProductController::class, 'update']);
                Route::delete('/{id}', [ProductController::class, 'destroy']);
            });
        });

        // --- Cart ---
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'show']);
            Route::post('/items', [CartController::class, 'addItem']);
            Route::put('/items/{id}', [CartController::class, 'updateItem']);
            Route::delete('/items/{id}', [CartController::class, 'removeItem']);
            Route::delete('/', [CartController::class, 'clear']);
        });

        // --- Orders ---
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);

            Route::get('/{id}', [OrderController::class, 'show']);
            Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
        });
    });
});
