<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $movement = new StockMovement();

        $this->assertEquals([
            'product_id',
            'type',
            'quantity',
            'reason',
            'reference_type',
            'reference_id',
        ], $movement->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $movement = new StockMovement();

        $this->assertFalse($movement->incrementing);
        $this->assertEquals('string', $movement->getKeyType());
    }

    /** @test */
    public function it_belongs_to_a_product()
    {
        $movement = new StockMovement();

        $relation = $movement->product();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }
}
