<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Str;

class CartService
{
    protected CartRepositoryInterface $carts;

    public function __construct(CartRepositoryInterface $carts)
    {
        $this->carts = $carts;
    }

    public function getOne($id)
    {
        $cart = Cart::find($id)->items();
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

    public function addItem(?string $cartId, string $userId, string $productId, int $quantity)
    {
        if ($cartId) {
            $cart = Cart::where('id', $cartId)
                ->where('user_id', $userId)
                ->firstOrFail();
        }
        else {
            $cart = Cart::create([
                'user_id' => $userId,
                'session_id' => Str::uuid(),
            ]);
        }

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
            return [$cart, $item];
        }

        $item = $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);

        return [$cart, $item];
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
