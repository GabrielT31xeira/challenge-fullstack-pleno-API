<?php

namespace App\Repositories\Cart;

interface CartRepositoryInterface
{
    public function getUserCart(string $userId);
    public function createUserCart(string $userId);
}