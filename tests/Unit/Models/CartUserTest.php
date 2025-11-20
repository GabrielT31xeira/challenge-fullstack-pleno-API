<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_has_items_relationship()
    {
        $user = User::factory()->has(Cart::factory()->count(3), 'carts')
            ->create();

        $this->assertCount(3, $user->carts);
    }
}