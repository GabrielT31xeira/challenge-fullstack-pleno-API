<?php

namespace App\Services;

use App\Repositories\Cart\CartRepository;

class CartService
{
    public function __construct(
        protected CartRepository $cartsRepository
    )
    {}

    public function getOne($id)
    {
        return $this->cartsRepository->getOne($id);
    }

    private function getOrCreateCart(string $userId)
    {
        $cart = $this->cartsRepository->getUserCart($userId);

        if (!$cart) {
            $cart = $this->cartsRepository->createUserCart($userId);
        }

        return $cart;
    }

    public function getCart(array $filters, string $userId)
    {
        return $this->cartsRepository->getUserCarts($filters, $userId);
    }

    public function createCart($cartData, $userId)
    {
        return $this->cartsRepository->createCart($cartData, $userId);
    }

    public function addItem($request, $user_id)
    {
        return $this->cartsRepository->addItem($request, $user_id);
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
