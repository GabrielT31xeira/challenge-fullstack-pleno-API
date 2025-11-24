<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use Illuminate\Support\Str;

class CartRepository implements CartRepositoryInterface
{
    public function getOne($id)
    {
        return Cart::with('items.product')->where('id', $id)->first() ;
    }
    
    public function getUserCarts(array $filters, string $userId)
    {
        $query = Cart::with('items.product');
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

    public function addItem($request, $user_id)
    {
        $cartId = $request['cart_id'] ?? null;

        if ($cartId !== null) {
            $cart = Cart::where('id', $cartId)
                ->where('user_id', $user_id)
                ->first();

            if (!$cart) {
                throw new \Exception("Carrinho não encontrado para o usuário");
            }
        } else {
            $cart = Cart::create([
                'user_id' => $user_id,
                'session_id' => Str::uuid(),
            ]);
        }

        $item = $cart->items()->where('product_id', $request['product_id'])->first();

        if ($item) {
            $item->quantity += $request['quantity'];
            $item->save();
            return $item;
        }

        $newItem = $cart->items()->create([
            'product_id' => $request['product_id'],
            'quantity' => $request['quantity'],
        ]);

        return $newItem;
    }

    public function createCart(array $cartData, string $userId)
    {
        return Cart::create([
            'user_id' => $userId,
            'name' => $cartData['name'],
            'session_id' => Str::uuid(),
        ]);
    }
}