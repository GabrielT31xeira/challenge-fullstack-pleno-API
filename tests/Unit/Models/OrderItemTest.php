<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $item = new OrderItem();

        $this->assertEquals([
            'order_id',
            'product_id',
            'quantity',
            'unit_price',
            'total_price',
        ], $item->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $item = new OrderItem();

        $this->assertFalse($item->incrementing);
        $this->assertEquals('string', $item->getKeyType());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $item = new OrderItem();

        $this->assertEquals([
            'price' => 'decimal:2',
            'total' => 'decimal:2',
        ], $item->getCasts());
    }

    /** @test */
    public function it_belongs_to_an_order()
    {
        $item = new OrderItem();

        $relation = $item->order();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('order_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function it_belongs_to_a_product()
    {
        $item = new OrderItem();

        $relation = $item->product();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }
}
