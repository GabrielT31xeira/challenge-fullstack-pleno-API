<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $order = new Order();

        $this->assertEquals([
            'id',
            'user_id',
            'status',
            'total',
            'subtotal',
            'tax',
            'shipping_cost',
            'shipping_address',
            'billing_address',
            'notes',
            'cart_id'
        ], $order->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $order = new Order();

        $this->assertFalse($order->incrementing);
        $this->assertEquals('string', $order->getKeyType());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $order = new Order();

        $this->assertEquals([
            'shipping_address' => 'array',
            'billing_address'  => 'array',
            'total'            => 'decimal:2',
            'subtotal'         => 'decimal:2',
            'tax'              => 'decimal:2',
            'shipping_cost'    => 'decimal:2',
        ], $order->getCasts());
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $order = new Order();

        $relation = $order->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function it_has_many_order_items()
    {
        $order = new Order();

        $relation = $order->items();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('order_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function it_belongs_to_a_cart()
    {
        $order = new Order();

        $relation = $order->cart();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('cart_id', $relation->getForeignKeyName());
    }
}
