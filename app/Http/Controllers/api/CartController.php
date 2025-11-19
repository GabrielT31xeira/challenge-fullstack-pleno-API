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
    protected CartService $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function show(Request $request)
    {
        try {
            $cart = $this->cart->getCart($request->user()->id);

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
            $cart = $this->cart->getOne($id);

            return ApiResponse::success(new CartResource($cart));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function addItem(AddItemCartRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user_id = $request->user()->id;
            $cart = $this->cart->addItem($request->validated(), $user_id);

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
            $cart = $this->cart->updateItem(
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
            $this->cart->removeItem($request->user()->id, $id);

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
            $this->cart->clear($request->user()->id);

            return ApiResponse::success();
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }
}
