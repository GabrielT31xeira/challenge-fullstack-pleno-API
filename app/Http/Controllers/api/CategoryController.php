<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\ProductCategoryResource;
use App\Services\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoriesService
    ){}

    public function index()
    {
        try {
            $tree = $this->categoriesService->allTree();
            $resource = CategoryResource::collection($tree);

            return ApiResponse::success($resource);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }

    public function paginated()
    {
        try {
            $tree = $this->categoriesService->paginated(request()->all());
            $resource = CategoryResource::collection($tree);
            return ApiResponse::paginated($resource);

        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }

    public function create(CategoryRequest $request)
    {
        try {
            $category = $this->categoriesService->create($request->validated());

            return ApiResponse::success(new CategoryResource($category));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoriesService->update($request->validated(), $id);

            return ApiResponse::success(new CategoryResource($category));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }

    public function delete($id)
    {
        try {
            $this->categoriesService->delete($id);

            return ApiResponse::success();
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }

    public function products($id)
    {
        try {
            $products = $this->categoriesService->findProductsByCategory($id);
            $resource = ProductCategoryResource::collection($products);
            return ApiResponse::paginated($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }
    }
}
