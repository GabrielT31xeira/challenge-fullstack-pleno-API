<?php

namespace Tests\Unit\Http\Resources\Cart;

use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\Cart\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_resource_returns_expected_array()
    {
        $cart = Cart::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'quantity' => 3,
        ]);

        $cart->load('items');

        $resource = new CartResource($cart);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertEquals($cart->id, $array['id']);
        $this->assertEquals($cart->user_id, $array['user_id']);
        $this->assertEquals($cart->session_id, $array['session_id']);

        $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/', $array['created_at']);
        $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/', $array['updated_at']);

        $this->assertCount(2, $array['items']);
        $this->assertInstanceOf(CartItemResource::class, $array['items'][0]);

        $this->assertEquals(2, $array['items_count']);
        $this->assertEquals(5, $array['total_quantity']);
    }

    public function test_cart_resource_without_items_returns_null_fields()
    {
        $cart = Cart::factory()->create();

        $resource = new CartResource($cart);
        $array = $resource->toArray(request());

        $this->assertArrayHasKey('items', $array);
    }
}
