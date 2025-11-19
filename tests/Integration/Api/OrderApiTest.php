<?php

namespace Tests\Integration\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = [
            'cart_id' => 'fake-cart',
            'shipping_address' => ['Rua Teste'],
            'billing_address' => ['Rua Teste'],
            'notes' => 'Entrega rápida'
        ];

        $this->actingAs($user)
            ->postJson('/api/orders', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'total',
                'status',
                'items'
            ]);
    }
}
