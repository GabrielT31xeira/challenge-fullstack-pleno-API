<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Cart\CartResource;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ){}

    public function show(Request $request)
    {
        try {
            $cart = $this->cartService->getCart($request->user()->id);

            return ApiResponse::success(new CartResource($cart));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function getOne($id)
    {
        try {
            $cart = $this->cartService->getOne($id);

            return ApiResponse::success(new CartResource($cart));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function addItem(AddItemCartRequest $request)
    {
        try {
            $user_id = $request->user()->id;
            $cart = $this->cartService->addItem($request->validated(), $user_id);

            return ApiResponse::success(new CartResource($cart));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }


    public function updateItem(UpdateCartRequest $request, $id)
    {
        try {
            $cart = $this->cartService->updateItem(
                $request->user()->id,
                $id,
                $request['quantity']
            );

            return ApiResponse::success(new CartResource($cart));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }

    }

    public function removeItem(Request $request, $id)
    {
        try {
            $this->cartService->removeItem($request->user()->id, $id);

            return ApiResponse::success();
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function clear(Request $request)
    {
        try {
            $this->cartService->clear($request->user()->id);

            return ApiResponse::success();
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }
}
