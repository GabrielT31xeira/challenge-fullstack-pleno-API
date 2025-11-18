<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{

    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
