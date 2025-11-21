<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\Product\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new ProductRepository();
    }

    /** @test */
    public function it_paginates_products_with_filters()
    {
        $category = Category::factory()->create();

        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'active' => true
        ]);

        $results = $this->repo->paginate([
            'category_id' => $category->id,
            'active' => true,
            'per_page' => 10
        ]);

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_filters_by_search()
    {
        Product::factory()->create(['name' => 'Super Notebook']);
        Product::factory()->create(['name' => 'Mesa Gamer']);

        $results = $this->repo->paginate([
            'search' => 'notebook'
        ]);

        $this->assertCount(1, $results);
        $this->assertEquals('Super Notebook', $results->first()->name);
    }

    /** @test */
    public function it_finds_product_with_relations()
    {
        $product = Product::factory()
            ->has(Tag::factory()->count(2))
            ->create();

        $found = $this->repo->find($product->id);

        $this->assertNotNull($found);
        $this->assertEquals($product->id, $found->id);
        $this->assertCount(2, $found->tags);
    }

    /** @test */
    public function it_creates_product_with_relations()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Smartphone X',
            'slug' => 'smartphone-x',
            'description' => 'Top de linha',
            'price' => 1999.90,
            'quantity' => 12,
            'active' => true,
            'category_id' => $category->id,
        ];

        $product = $this->repo->create($data);

        $this->assertEquals('Smartphone X', $product->name);
    }

    /** @test */
    public function it_updates_product_and_syncs_tags()
    {
        $product = Product::factory()->create();

        $updated = $this->repo->update($product->id, [
            'name' => 'Updated',
        ]);

        $this->assertEquals('Updated', $updated->name);
        $this->assertEmpty($product->tags);
    }

    /** @test */
    public function it_checks_if_slug_exists()
    {
        Product::factory()->create(['slug' => 'iphone-15']);

        $exists = $this->repo->slugExists('iphone-15');

        $this->assertTrue($exists);
    }

    /** @test */
    public function it_checks_if_slug_exists_ignoring_id()
    {
        $product = Product::factory()->create(['slug' => 'galaxy-s24']);

        $exists = $this->repo->slugExists('galaxy-s24', $product->id);

        $this->assertFalse($exists);
    }

    /** @test */
    public function it_deletes_a_product()
    {
        $product = Product::factory()->create();

        $deleted = $this->repo->delete($product->id);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function it_filters_products_with_sorting()
    {
        Product::factory()->create(['name' => 'Z Product', 'created_at' => now()]);
        Product::factory()->create(['name' => 'A Product', 'created_at' => now()->addMinute()]);

        $filters = [
            'sort' => 'name',
            'direction' => 'asc',
            'per_page' => 10
        ];

        $result = $this->repo->paginate($filters);

        $this->assertCount(2, $result);
        $this->assertEquals('A Product', $result->first()->name);
        $this->assertEquals('Z Product', $result->last()->name);
    }

    /** @test */
    public function it_filters_products_with_sorting_and_desc_direction()
    {
        Product::factory()->create(['name' => 'A Product', 'created_at' => now()]);
        Product::factory()->create(['name' => 'Z Product', 'created_at' => now()->addMinute()]);

        $filters = [
            'sort' => 'name',
            'direction' => 'desc',
            'per_page' => 10
        ];

        $result = $this->repo->paginate($filters);

        $this->assertCount(2, $result);
        $this->assertEquals('Z Product', $result->first()->name);
        $this->assertEquals('A Product', $result->last()->name);
    }

    /** @test */
    public function it_filters_products_with_sorting_and_default_direction()
    {
        Product::factory()->create(['name' => 'Z Product', 'price' => 100]);
        Product::factory()->create(['name' => 'A Product', 'price' => 200]);

        $filters = [
            'sort' => 'name',
            'per_page' => 10
        ];

        $result = $this->repo->paginate($filters);

        $this->assertCount(2, $result);
        $this->assertEquals('A Product', $result->first()->name);
        $this->assertEquals('Z Product', $result->last()->name);
    }
    /** @test */
    public function it_finds_product_with_relations_loaded()
    {
        $category = Category::factory()->create();
        $product = Product::factory()
            ->for($category)
            ->has(Tag::factory()->count(3))
            ->create();

        $found = $this->repo->findWithRelations($product->id);

        $this->assertNotNull($found);
        $this->assertEquals($product->id, $found->id);

        $this->assertTrue($found->relationLoaded('category'));
        $this->assertTrue($found->relationLoaded('tags'));

        $this->assertEquals($category->id, $found->category->id);
        $this->assertCount(3, $found->tags);
    }

    /** @test */
    public function it_returns_null_when_product_not_found_with_relations()
    {
        $found = $this->repo->findWithRelations('non-existent-id');

        $this->assertNull($found);
    }
}
