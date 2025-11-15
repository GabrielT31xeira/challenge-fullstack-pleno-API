<?php

namespace App\Services;

use App\Models\CartItem;
use App\Repositories\Cart\CartRepositoryInterface;

class CartService
{
    protected CartRepositoryInterface $carts;

    public function __construct(CartRepositoryInterface $carts)
    {
        $this->carts = $carts;
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

    public function addItem(string $userId, string $productId, int $quantity)
    {
        $cart = $this->getOrCreateCart($userId);

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
            return $item;
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    public function updateItem(string $userId, string $itemId, int $quantity)
    {

        $cart = $this->getOrCreateCart($userId);
        $item = $cart->items()->where('product_id', $itemId)->firstOrFail();

        $item->update([
            'quantity' => $quantity
        ]);

        return $item;
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
