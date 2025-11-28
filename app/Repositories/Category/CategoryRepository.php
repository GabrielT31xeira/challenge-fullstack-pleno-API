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
            ->get();
    }

    public function create(array $categoryData)
    {
        return Category::create([
            'name' => $categoryData['name'],
            'slug' => $categoryData['slug'],
            'description' => $categoryData['description'] ?? null,
            'active' => $categoryData['active'] ?? true,
            'parent_id' => $categoryData['parent_id'] ?? null,
        ]);
    }

    public function update(array $categoryData, $id)
    {
        $category = Category::where('id', $id)->first();
        return $category->update([
            'name' => $categoryData['name'],
            'slug' => $categoryData['slug'],
            'description' => $categoryData['description'] ?? null,
            'active' => $categoryData['active'] ?? true,
            'parent_id' => $categoryData['parent_id'] ?? null,
        ]);
    }

    public function delete($id)
    {
        return Category::destroy($id);
    }

    public function paginated(array $filters)
    {
        $query = Category::with([
            'children' => function ($query) {
                $query->with('children');
            }])
            ->whereNull('parent_id')
            ->orderBy('name');

        if (!empty($filters['search']))  {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate(9);
    }

    public function findProductsByCategory(string $id)
    {
        $category = Category::findOrFail($id);

        return $category->products()
            ->with(['tags', 'category'])
            ->paginate(10);
    }
}
