<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;

class CartSeeder extends Seeder
{
    public function run()
    {
        Cart::factory(15)
            ->create()
            ->each(function ($cart) {
                CartItem::factory(rand(1, 4))->create([
                    'cart_id' => $cart->id,
                ]);
            });
    }
}
