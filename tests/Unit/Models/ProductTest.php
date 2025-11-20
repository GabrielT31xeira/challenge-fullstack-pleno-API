<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $product = new Product();

        $this->assertEquals([
            'name',
            'slug',
            'description',
            'price',
            'cost_price',
            'quantity',
            'min_quantity',
            'active',
            'category_id',
            'deleted_at'
        ], $product->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $product = new Product();

        $this->assertFalse($product->incrementing);
        $this->assertEquals('string', $product->getKeyType());
    }

    /** @test */
    public function it_has_soft_deletes_enabled()
    {
        $product = new Product();

        $this->assertTrue(method_exists($product, 'runSoftDelete'));
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $product = new Product();

        $relation = $product->category();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('category_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function it_belongs_to_many_tags()
    {
        $product = new Product();

        $relation = $product->tags();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    /** @test */
    public function it_has_many_order_items()
    {
        $product = new Product();

        $relation = $product->orderItems();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function it_has_many_stock_movements()
    {
        $product = new Product();

        $relation = $product->stockMovements();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }
}
