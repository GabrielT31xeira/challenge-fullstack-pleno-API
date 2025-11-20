<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Repositories\Category\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new CategoryRepository();
    }

    // -----------------------------------------------------
    // allTree()
    // -----------------------------------------------------
    public function test_all_tree_returns_only_root_categories()
    {
        // Root categories
        $root1 = Category::factory()->create(['parent_id' => null, 'name' => 'B']);
        $root2 = Category::factory()->create(['parent_id' => null, 'name' => 'A']);

        // Child
        Category::factory()->create([
            'parent_id' => $root1->id,
            'name' => 'Child',
        ]);

        $result = $this->repo->allTree();

        $this->assertCount(2, $result); // only root categories
        $this->assertEquals('A', $result[0]->name); // sorted
        $this->assertEquals('B', $result[1]->name);

        $this->assertTrue($result[1]->relationLoaded('children'));
        $this->assertCount(1, $result[1]->children);
    }

    public function test_all_tree_paginates_results()
    {
        // Create 15 root categories
        Category::factory()->count(15)->create(['parent_id' => null]);

        $result = $this->repo->allTree();

        $this->assertEquals(10, $result->count());
        $this->assertEquals(15, $result->total());
    }

    // -----------------------------------------------------
    // findProductsByCategory()
    // -----------------------------------------------------
    public function test_find_products_by_category_returns_paginated_products()
    {
        $category = Category::factory()->create();

        $product1 = Product::factory()->for($category)->create();
        $product2 = Product::factory()->for($category)->create();

        $tag = Tag::factory()->create();

        $product1->tags()->attach([$tag->id]);

        $result = $this->repo->findProductsByCategory($category->id);

        $this->assertEquals(2, $result->count());
        $this->assertTrue($result[0]->relationLoaded('tags'));
        $this->assertTrue($result[0]->relationLoaded('category'));
    }

    public function test_find_products_by_category_throws_not_found_if_invalid_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repo->findProductsByCategory('invalid-id');
    }
}
