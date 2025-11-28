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

    public function paginated(array $filters)
    {
        return $this->categoryRepository->paginated($filters);
    }

    public function create($categoryData)
    {
        return $this->categoryRepository->create($categoryData);
    }

    public function update($categoryData, $id)
    {
        return $this->categoryRepository->update($categoryData, $id);
    }

    public function delete($id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function findProductsByCategory(string $id)
    {
        return $this->categoryRepository->findProductsByCategory($id);
    }
}
