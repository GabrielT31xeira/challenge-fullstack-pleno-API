<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\ProductResource;
use App\Services\CategoryService;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    protected $categories;

    public function __construct(CategoryService $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        try {
            $tree = $this->categories->allTree();
            $resource = CategoryResource::collection($tree);

            return ApiResponse::paginated($resource);

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
            $products = $this->categories->findProductsByCategory($id);
            $resource = ProductResource::collection($products);
            return ApiResponse::paginated($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar categorias",
                $th->getMessage()
            );
        }

    }
}
