<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\ProductCategoryResource;
use App\Services\CategoryService;
use App\Support\ApiResponse;

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
