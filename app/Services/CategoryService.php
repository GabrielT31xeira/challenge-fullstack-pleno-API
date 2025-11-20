<?php

namespace App\Services;

use App\Repositories\Category\CategoryRepository;

class CategoryService
{
    public function __construct(protected CategoryRepository $categoryRepository)
    {
    }
    public function allTree()
    {
        return $this->categoryRepository->allTree();
    }

    public function findProductsByCategory(string $id)
    {
        return $this->categoryRepository->findProductsByCategory($id);
    }
}
