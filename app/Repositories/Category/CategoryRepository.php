<?php

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allTree()
    {
        return Category::with([
            'children' => function ($query) {
                $query->with('children');
            }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->paginate(10);
    }

    public function findProductsByCategory(string $id)
    {
        return Category::with('products.tags', 'products.category')
            ->findOrFail($id)
            ->products()
            ->paginate(10);
    }
}
