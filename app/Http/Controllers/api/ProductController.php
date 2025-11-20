<?php

namespace App\Http\Controllers\api;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Services\ProductService;
use App\Support\ApiResponse;
use Illuminate\Validation\ValidationException;

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
            $dto = CreateProductDTO::fromRequest($request);
            $dto->validateBusinessRules();

            $product = $this->products->create($dto);

            return ApiResponse::success(
                new ProductResource($product)
            );

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao criar o produto",
                $th->getMessage()
            );
        }
    }

    public function update(ProductUpdateRequest $request, string $productId)
    {
        try {
            $dto = UpdateProductDTO::fromRequest($request, $productId);
            $dto->validateBusinessRules();

            $product = $this->products->update($dto);

            return ApiResponse::success(
                new ProductResource($product),
            );

        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage());
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao atualizar produto",
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
