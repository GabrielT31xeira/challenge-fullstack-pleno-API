<?php

namespace Tests\Integration\Api;

use App\Models\Cart;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_create_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'shipping_address' => ['Rua Teste'],
            'billing_address' => ['Rua Teste'],
            'notes' => 'Entrega rápida'
        ];

        $this->postJson('/api/v1/orders', $payload)
            ->assertStatus(200);

    }
}
