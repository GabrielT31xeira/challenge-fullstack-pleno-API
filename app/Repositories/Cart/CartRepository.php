<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Str;

class CartRepository implements CartRepositoryInterface
{
    public function getOne($id)
    {
        return Cart::with('items.product')->where('id', $id)->first() ;
    }

    public function getAll(string $userId)
    {
        return Cart::where('user_id', $userId)->get();
    }
    
    public function getUserCarts(array $filters, string $userId)
    {
        $query = Cart::where('user_id', $userId)
            ->with('items.product');
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate(9);
    }

    public function createUserCart(string $userId)
    {
        return Cart::create([
            'user_id' => $userId,
        ]);
    }

    public function createCart(array $cartData, string $userId)
    {
        return Cart::create([
            'user_id' => $userId,
            'name' => $cartData['name'],
            'session_id' => Str::uuid(),
        ]);
    }

    public function addItem($request, $user_id)
    {
        $cartId = $request['cart_id'] ?? null;
        if ($cartId !== null) {
            $cart = Cart::where('id', $cartId)
                ->first();

            if (!$cart) {
                return null;
            }
        } else {
            $quantity = Cart::where('user_id', $user_id)->get()->count();
            $cart = Cart::create([
                'user_id' => $user_id,
                'session_id' => Str::uuid(),
                'name' => 'Carrinho '. $quantity,
            ]);
        }

        $item = $cart->items()->where('product_id', $request['product_id'])->first();

        if ($item) {
            $item->quantity += $request['quantity'];
            $item->save();
            return $item;
        }

        return $cart->items()->create([
            'product_id' => $request['product_id'],
            'quantity'   => $request['quantity'],
        ]);
    }

    public function updateItem($productId, Cart $cart, $quantity)
    {
        $item = $cart->items()->where('product_id', $productId)->firstOrFail();

        $item->update([
            'quantity' => $quantity
        ]);

        return $cart->fresh(['items']);
    }

    public function removeItem($id, Cart $cart) {
        return $cart->items()->where('product_id', $id)
            ->firstOrFail()
            ->delete();
    }
}