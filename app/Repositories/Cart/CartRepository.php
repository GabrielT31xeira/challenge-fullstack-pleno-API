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

    public function addItem($request, $user_id)
    {
        $cartId = array_key_exists('cart_id', $request) ? $request['cart_id'] : null;

        if ($cartId !== null) {
            $cart = Cart::where('id', $request['cart_id'])
                ->where('user_id', $user_id);
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
            return [$cart, $item];
        }

        $cart->items()->create([
            'product_id' => $request['product_id'],
            'quantity' => $request['quantity'],
        ]);

        return $cart;
    }
}