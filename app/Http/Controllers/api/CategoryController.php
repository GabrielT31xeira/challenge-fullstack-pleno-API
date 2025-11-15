<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categories;

    public function __construct(CategoryService $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        $tree = $this->categories->listTree();
        return \App\Http\Resources\Category\CategoryResource::collection($tree);
    }

    public function products($id)
    {
        $products = $this->categories->listProducts($id);
        return \App\Http\Resources\Category\ProductResource::collection($products);
    }
}
