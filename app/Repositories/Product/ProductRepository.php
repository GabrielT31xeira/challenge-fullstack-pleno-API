<?php

namespace App\Repositories\Product;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters)
    {
        $query = Product::with(['category', 'tags']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('slug', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['sort'])) {
            $query->orderBy($filters['sort'], $filters['direction'] ?? 'asc');
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function find(string $id)
    {
        return Product::with(['category', 'tags'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $product = Product::create($data);

        if (!empty($data['tags'])) {
            $product->tags()->sync($data['tags']);
        }

        return $product->load(['category', 'tags']);
    }

    public function update(string $id, array $data)
    {
        $product = Product::findOrFail($id);

        $product->update($data);

        if (isset($data['tags'])) {
            $product->tags()->sync($data['tags']);
        }

        return $product->load(['category', 'tags']);
    }

    public function delete(string $id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }
}
