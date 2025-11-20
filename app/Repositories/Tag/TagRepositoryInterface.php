<?php

namespace App\Repositories\Tag;

interface TagRepositoryInterface
{
    public function findMany(array $ids);
}