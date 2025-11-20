<?php

namespace Tests\Unit\Services;

use App\Repositories\Category\CategoryRepository;
use App\Services\CategoryService;
use Mockery;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    protected CategoryRepository $categoryRepository;
    protected CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);

        $this->service = new CategoryService(
            $this->categoryRepository
        );
    }

    /** @test */
    public function it_returns_all_categories_in_tree_structure()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'Eletrônicos',
                'children' => [
                    ['id' => 2, 'name' => 'Celulares'],
                    ['id' => 3, 'name' => 'TVs'],
                ]
            ]
        ];

        $this->categoryRepository
            ->shouldReceive('allTree')
            ->once()
            ->andReturn($expected);

        $result = $this->service->allTree();

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_returns_products_by_category_id()
    {
        $categoryId = '10';

        $expectedProducts = [
            ['id' => 1, 'name' => 'Notebook'],
            ['id' => 2, 'name' => 'Mouse'],
        ];

        $this->categoryRepository
            ->shouldReceive('findProductsByCategory')
            ->with($categoryId)
            ->once()
            ->andReturn($expectedProducts);

        $result = $this->service->findProductsByCategory($categoryId);

        $this->assertSame($expectedProducts, $result);
    }
}
