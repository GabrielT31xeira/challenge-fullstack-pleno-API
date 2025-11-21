<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = false;

    public function test_full_order_creation_flow()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create(['price' => 100.00, 'quantity' => 100]);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test-token')->plainTextToken;

        $cartResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/v1/cart/items',[
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $cartResponse->assertStatus(200);
        $this->assertGreaterThan(0, count($cartResponse->json('data')), 'Carrinho deve ter itens');

        $response = $this->postJson('/api/v1/orders', [
            'cart_id' => $cart->id,
            'shipping_address' => ['Endereço teste'],
            'billing_address' => ['Endereço teste']
        ]);

        $response->assertStatus(200);
    }
}