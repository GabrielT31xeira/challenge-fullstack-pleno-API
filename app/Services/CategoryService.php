<?php

namespace App\Services;

use App\Repositories\Category\CategoryRepositoryInterface;

class CategoryService
{
    protected $categories;

    public function __construct(CategoryRepositoryInterface $categories)
    {
        $this->categories = $categories;
    }

    public function listTree()
    {
        return $this->categories->allTree();
    }

    public function listProducts(string $categoryId)
    {
        return $this->categories->findProductsByCategory($categoryId);
    }
}
