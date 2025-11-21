<?php

namespace Tests\Unit\Http\Resources\Product;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Tags\TagsResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_resource_returns_expected_array()
    {
        $category = Category::factory()->create([
            'name' => 'Eletrônicos',
        ]);

        $tags = Tag::factory()->count(2)->create();

        $product = Product::factory()->create([
            'name' => 'Smartphone',
            'slug' => 'smartphone',
            'description' => 'Um celular top',
            'price' => 1999.99,
            'cost_price' => 1500.00,
            'quantity' => 10,
            'min_quantity' => 1,
            'active' => true,
            'category_id' => $category->id,
        ]);

        $product->tags()->attach($tags->pluck('id')->toArray());

        $product->load(['category', 'tags']);

        $resource = new ProductResource($product);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertEquals($product->id, $array['id']);
        $this->assertEquals('Smartphone', $array['name']);
        $this->assertEquals(1999.99, $array['price']);
        $this->assertEquals(1500.00, $array['cost_price']);
        $this->assertEquals(10, $array['quantity']);
        $this->assertEquals(1, $array['min_quantity']);
        $this->assertTrue($array['active']);
        $this->assertEquals($category->id, $array['category_id']);

        $this->assertArrayHasKey('category', $array);
        $this->assertInstanceOf(CategoryResource::class, $array['category']);
        $this->assertEquals('Eletrônicos', $array['category']->resource->name);

        $this->assertArrayHasKey('tags', $array);
        $this->assertCount(2, $array['tags']);
        $this->assertInstanceOf(TagsResource::class, $array['tags'][0]);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }
}
