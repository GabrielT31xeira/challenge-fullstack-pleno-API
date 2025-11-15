<?php

namespace App\Repositories\Category;

interface CategoryRepositoryInterface
{
    public function allTree();
    public function findProductsByCategory(string $id);
}