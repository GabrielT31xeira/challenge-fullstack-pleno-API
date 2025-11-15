<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function paginate(array $filters);
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
