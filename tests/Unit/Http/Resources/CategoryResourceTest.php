<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_resource_returns_expected_array()
    {
        $parent = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'description' => 'Categoria principal',
            'active' => true,
        ]);

        $child = Category::factory()->create([
            'name' => 'Celulares',
            'slug' => 'celulares',
            'description' => 'Subcategoria',
            'active' => true,
            'parent_id' => $parent->id,
        ]);

        $parent->load('children');

        $resource = new CategoryResource($parent);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertEquals($parent->id, $array['id']);
        $this->assertEquals('Eletrônicos', $array['name']);
        $this->assertEquals('eletronicos', $array['slug']);
        $this->assertEquals('Categoria principal', $array['description']);
        $this->assertTrue($array['active']);

        $this->assertArrayHasKey('children', $array);
        $this->assertCount(1, $array['children']);
        $this->assertEquals($child->id, $array['children'][0]->id);
        $this->assertEquals('Celulares', $array['children'][0]->name);
    }

    public function test_category_resource_without_children_returns_empty_array()
    {
        $category = Category::factory()->create([
            'name' => 'Informática',
            'slug' => 'informatica',
            'description' => 'Categoria sem filhos',
            'active' => true,
        ]);

        $resource = new CategoryResource($category);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertArrayHasKey('children', $array);
    }
}
