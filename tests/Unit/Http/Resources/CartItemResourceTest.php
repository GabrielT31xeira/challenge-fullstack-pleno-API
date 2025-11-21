<?php

namespace Tests\Unit\Http\Resources\Cart;

use App\Http\Resources\Cart\CartItemResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_item_resource_returns_expected_array()
    {
        $product = Product::factory()->create([
            'name' => 'Produto Teste',
            'price' => 100.50,
        ]);

        $cart = Cart::factory()->create();

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $cartItem->load(['product', 'cart']);

        $resource = new CartItemResource($cartItem);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertEquals($cartItem->id, $array['id']);
        $this->assertEquals($cartItem->cart_id, $array['cart_id']);
        $this->assertEquals($cartItem->product_id, $array['product_id']);
        $this->assertEquals(2, $array['quantity']);

        $this->assertInstanceOf(ProductResource::class, $array['product']);
        $this->assertInstanceOf(CartResource::class, $array['cart']);

        $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/', $array['created_at']);
        $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/', $array['updated_at']);
    }
}
