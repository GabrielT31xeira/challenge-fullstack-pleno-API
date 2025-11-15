<?php

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allTree()
    {
        return Category::with('children.children.children')
            ->whereNull('parent_id')
            ->get();
    }

    public function findProductsByCategory(string $id)
    {
        return Category::with('products.tags', 'products.category')
            ->findOrFail($id)
            ->products;
    }
}
