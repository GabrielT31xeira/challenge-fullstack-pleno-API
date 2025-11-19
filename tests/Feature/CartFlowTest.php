<?php

namespace Tests\Feature;

use App\Models\Cart;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_item_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $response->assertStatus(201);

        $cart = Cart::where('user_id', $user->id)->first();

        $this->assertNotNull($cart, "Nenhum carrinho foi criado automaticamente.");

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

}
