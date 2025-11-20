<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $category = new Category();

        $this->assertEquals([
            'name',
            'slug',
            'description',
            'parent_id',
            'active',
        ], $category->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $category = new Category();

        $this->assertFalse($category->incrementing);
        $this->assertEquals('string', $category->getKeyType());
    }

    /** @test */
    public function it_has_parent_relationship()
    {
        $category = new Category();

        $this->assertInstanceOf(BelongsTo::class, $category->parent());
        $this->assertEquals('parent_id', $category->parent()->getForeignKeyName());
    }

    /** @test */
    public function it_has_children_relationship()
    {
        $category = new Category();

        $this->assertInstanceOf(HasMany::class, $category->children());
        $this->assertEquals('parent_id', $category->children()->getForeignKeyName());
    }

    /** @test */
    public function it_has_products_relationship()
    {
        $category = new Category();

        $this->assertInstanceOf(HasMany::class, $category->products());
        $this->assertEquals('category_id', $category->products()->getForeignKeyName());
    }
}
