<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\AdminController;

Route::prefix('v1')->group(function () {

    // --- Auth ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // --- public routes ---
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::middleware('verify-token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);

        // --- Products ---
        Route::prefix('products')->group(function () {
            Route::middleware('admin')->group(function () {
                Route::post('/', [ProductController::class, 'store']);
                Route::put('/{id}', [ProductController::class, 'update']);
                Route::delete('/{id}', [ProductController::class, 'destroy']);
            });
        });

        // --- Cart ---
        Route::prefix('cart')->group(function () {
            Route::get('/getAll', [CartController::class, 'getAll']);
            Route::get('/', [CartController::class, 'showPaginated']);
            Route::get('/{id}', [CartController::class, 'getOne']);
            Route::post('/', [CartController::class, 'create']);
            Route::post('/items', [CartController::class, 'addItem']);
            Route::put('{cart_id}/items/{product_id}', [CartController::class, 'updateItem']);
            Route::delete('{cart_id}/items/{product_id}', [CartController::class, 'removeItem']);
            Route::delete('/{cart_id}/clear', [CartController::class, 'clear']);
            Route::delete('/{cart_id}/delete', [CartController::class, 'deleteCart']);
        });

        // --- Orders ---
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);

            Route::get('/{id}', [OrderController::class, 'show']);
            Route::put('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('admin');
        });

        // --- admin routes ---
        Route::prefix('admin')->group(function () {
            Route::middleware('admin')->group(function () {
                Route::get('/dashboard', [AdminController::class, 'dashboard']);
            });
        });
    });
});

// Apenas para teste
Route::middleware(['admin'])->group(function () {
    Route::get('/admin-route', function () {
        return response()->json(['success' => true, 'message' => 'Acesso permitido']);
    });
});
