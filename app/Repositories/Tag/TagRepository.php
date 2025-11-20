<?php

namespace App\Repositories\Tag;

use App\Models\Tag;

class TagRepository implements TagRepositoryInterface
{
    public function __construct(private Tag $model) {}

    public function findMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}