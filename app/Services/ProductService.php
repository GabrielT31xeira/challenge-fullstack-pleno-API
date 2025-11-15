<?php

namespace App\Services;

use App\Repositories\Product\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $products
    ) {}

    public function list(array $filters)
    {
        return $this->products->paginate($filters);
    }

    public function show(string $id)
    {
        return $this->products->find($id);
    }

    public function create(array $data)
    {
        $data['slug'] = str($data['name'])->slug();
        return $this->products->create($data);
    }

    public function update(string $id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = str($data['name'])->slug();
        }

        return $this->products->update($id, $data);
    }

    public function delete(string $id)
    {
        return $this->products->delete($id);
    }
}
