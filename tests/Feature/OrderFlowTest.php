<?php

namespace Tests\Feature;

use App\Models\Cart;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_order_creation_flow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertStatus(201);

        $cartId = $response->json('item.cart_id');

        $payload = [
            'cart_id' => $cartId,
            'shipping_address' => ['Rua Exemplo, 123'],
            'billing_address' => ['Rua Exemplo, 123'],
            'notes' => 'Entregar à tarde'
        ];

        $this->postJson('/api/v1/orders', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'cart_id' => $cartId,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

}
