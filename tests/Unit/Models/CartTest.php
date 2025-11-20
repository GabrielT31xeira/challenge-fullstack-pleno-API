<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CartItem;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_has_items_relationship()
    {
        $cart = Cart::factory()->has(CartItem::factory()->count(3), 'items')
            ->create();

        $this->assertCount(3, $cart->items);
    }
}