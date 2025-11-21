<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Category\ProductCategoryResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_category_resource_returns_expected_array()
    {
        $category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'description' => 'Categoria principal',
            'active' => true,
        ]);

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

        $product->load('category');

        $resource = new ProductCategoryResource($product);
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

        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }
}
