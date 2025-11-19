<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Services\ProductService;
use App\Support\ApiResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $products
    ) {}

    public function index()
    {
        try {
            $response = $this->products->list(request()->all());
            $resource = ProductResource::collection($response);

            return ApiResponse::paginated($resource);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar produtos",
                $th->getMessage()
            );
        }
    }

    public function show($id)
    {
        try {
            $response = $this->products->show($id);

            if ($response->deleted_at !== null) {
                return ApiResponse::error(
                    "Esse produto foi apagado.",
                );
            }

            return ApiResponse::success($response);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar o produto",
                $th->getMessage()
            );
        }
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            $response = $this->products->create($request->toArray());

            return ApiResponse::success($response);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao criar o produto",
                $th->getMessage()
            );
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        try {
            $response = $this->products->update($id, $request->toArray());

            return ApiResponse::success($response);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao atualizar o produto",
                $th->getMessage()
            );
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->products->delete($id);

            return ApiResponse::success($response);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao remover o produto",
                $th->getMessage()
            );
        }
    }
}
