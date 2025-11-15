<?php

namespace App\Repositories\Cart;

use App\Models\Cart;

class CartRepository implements CartRepositoryInterface
{
    public function getUserCart(string $userId)
    {
        return Cart::with('items.product')->where('user_id', $userId)->first();
    }

    public function createUserCart(string $userId)
    {
        return Cart::create([
            'user_id' => $userId,
        ]);
    }
}