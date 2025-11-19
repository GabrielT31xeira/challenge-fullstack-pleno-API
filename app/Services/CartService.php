<?php

namespace App\Services;

use App\Repositories\Cart\CartRepository;

class CartService
{
    protected CartRepository $carts;

    public function __construct(CartRepository $carts)
    {
        $this->carts = $carts;
    }

    public function getOne($id)
    {
        $cart = $this->carts->getOne($id);
        return $cart;
    }

    private function getOrCreateCart(string $userId)
    {
        $cart = $this->carts->getUserCart($userId);

        if (!$cart) {
            $cart = $this->carts->createUserCart($userId);
        }

        return $cart;
    }

    public function getCart(string $userId)
    {
        return $this->getOrCreateCart($userId);
    }

    public function addItem($request, $user_id)
    {
        return $this->carts->addItem($request, $user_id);
    }


    public function updateItem(string $userId, string $itemId, int $quantity)
    {
        $cart = $this->getOrCreateCart($userId);
        $item = $cart->items()->where('product_id', $itemId)->firstOrFail();

        $item->update([
            'quantity' => $quantity
        ]);

        return $cart;
    }

    public function removeItem(string $userId, string $itemId)
    {
        $cart = $this->getOrCreateCart($userId);

        $item = $cart->items()->where('product_id', $itemId)->firstOrFail();

        return $item->delete();
    }

    public function clear(string $userId)
    {
        $cart = $this->getOrCreateCart($userId);

        return $cart->items()->delete();
    }
}
