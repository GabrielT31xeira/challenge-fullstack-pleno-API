<?php

namespace Tests\Feature;

use App\Models\Cart;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;

class StockUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_updates_after_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 5]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'shipping_address' => ['Rua A'],
            'billing_address' => ['Rua A'],
        ];

        echo "User ID: " . $user->id . "\n";
        echo "Cart ID: " . $cart->id . "\n";
        echo "Product stock: " . $product->quantity . "\n";
        echo "Cart items count: " . $cart->items()->count() . "\n";

        $response = $this->actingAs($user)->postJson('/api/v1/orders', $payload);

        echo "Status: " . $response->status() . "\n";
        echo "Response: " . $response->getContent() . "\n";

        $this->assertEquals(200, $response->status(), "A API deveria retornar status 200");

        $updatedProduct = Product::find($product->id);
        $this->assertEquals(3, $updatedProduct->quantity);
    }
}
