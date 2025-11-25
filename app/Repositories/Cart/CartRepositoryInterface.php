<?php

namespace App\Repositories\Cart;

interface CartRepositoryInterface
{
    public function getOne($id);
    public function getUserCarts(array $filters, string $userId);
    public function createUserCart(string $userId);
}