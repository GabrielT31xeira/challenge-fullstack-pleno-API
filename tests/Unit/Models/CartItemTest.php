<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $cartItem = new CartItem();

        $this->assertEquals([
            'cart_id',
            'product_id',
            'quantity',
        ], $cartItem->getFillable());
    }

    public function test_has_uuid_as_primary_key()
    {
        $cartItem = new CartItem();

        $this->assertFalse($cartItem->incrementing);
        $this->assertEquals('string', $cartItem->getKeyType());
    }

    public function test_cart_relationship()
    {
        $cartItem = new CartItem();

        $relation = $cartItem->cart();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('cart_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
        $this->assertEquals(Cart::class, $relation->getModel()::class);
    }

    public function test_product_relationship()
    {
        $cartItem = new CartItem();

        $relation = $cartItem->product();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
        $this->assertEquals(Product::class, $relation->getModel()::class);
    }
}
