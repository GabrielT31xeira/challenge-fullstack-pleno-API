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

    /**
     * -------------------------
     *  AUTH (Público)
     * -------------------------
     */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    /**
     * -------------------------
     *  ROTAS PÚBLICAS
     * -------------------------
     */

    // Categorias
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

    // Produtos
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);


    /**
     * -------------------------
     *  ROTAS PROTEGIDAS (Usuário logado)
     * -------------------------
     */
    Route::middleware('verify-token')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // Perfil
        Route::get('/profile', [ProfileController::class, 'show']);


        /**
         * -------------------------
         *  PRODUTOS (Admin)
         * -------------------------
         */
        Route::prefix('products')->middleware('admin')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });


        /**
         * -------------------------
         *  CARRINHO
         * -------------------------
         */
        Route::prefix('cart')->group(function () {

            // Carrinhos do usuário
            Route::get('/getAll', [CartController::class, 'getAll']);
            Route::get('/', [CartController::class, 'showPaginated']);
            Route::get('/{id}', [CartController::class, 'getOne']);
            Route::post('/', [CartController::class, 'create']);

            // Itens do carrinho
            Route::post('/items', [CartController::class, 'addItem']);
            Route::put('/{cart_id}/items/{product_id}', [CartController::class, 'updateItem']);
            Route::delete('/{cart_id}/items/{product_id}', [CartController::class, 'removeItem']);

            // Gerenciamento do carrinho
            Route::delete('/{cart_id}/clear', [CartController::class, 'clear']);
            Route::delete('/{cart_id}/delete', [CartController::class, 'deleteCart']);
        });


        /**
         * -------------------------
         *  PEDIDOS
         * -------------------------
         */
        Route::prefix('orders')->group(function () {

            // Cliente
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{id}', [OrderController::class, 'show']);

            // Admin
            Route::put('/{id}/status', [OrderController::class, 'updateStatus'])
                ->middleware('admin');
        });


        /**
         * -------------------------
         *  ROTAS ADMIN
         * -------------------------
         */
        Route::prefix('admin')->middleware('admin')->group(function () {

            // Dashboard e gerais
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/tags', [AdminController::class, 'gelAllTags']);

            // Categorias
            Route::prefix('categories')->group(function () {
                Route::post('/create', [CategoryController::class, 'create']);
                Route::put('/update/{id}', [CategoryController::class, 'update']);
                Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
                Route::get('/paginated', [CategoryController::class, 'paginated']);
            });

            // Produtos
            Route::prefix('products')->group(function () {
                Route::get('/lowstock', [AdminController::class, 'lowStock']);
                Route::get('/orders', [AdminController::class, 'order']);
            });
        });

    });
});


// ---------------------------
//  ROTA DE TESTE (Admin)
// ---------------------------
Route::middleware(['admin'])->group(function () {
    Route::get('/admin-route', function () {
        return response()->json([
            'success' => true,
            'message' => 'Acesso permitido'
        ]);
    });
});
