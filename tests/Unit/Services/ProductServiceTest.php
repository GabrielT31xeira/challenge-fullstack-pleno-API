<?php

namespace Tests\Unit\Services;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Models\Product;
use App\Models\Tag;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Tag\TagRepository;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected ProductRepository $productRepository;
    protected TagRepository $tagRepository;
    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->tagRepository = Mockery::mock(TagRepository::class);

        $this->service = new ProductService(
            $this->productRepository,
            $this->tagRepository
        );
    }

    /** @test */
    public function it_creates_a_product_without_tags()
    {
        $dto = Mockery::mock(CreateProductDTO::class);
        $dto->shouldReceive('toArray')->andReturn(['name' => 'Produto']);
        $dto->shouldReceive('getTags')->andReturn([]);

        $product = Mockery::mock(Product::class);
        $product->shouldReceive('load')->with(['category', 'tags'])->andReturn($product);

        $this->productRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($product);

        $result = $this->service->create($dto);

        $this->assertSame($product, $result);
    }

    /** @test */
    public function it_creates_a_product_and_associates_tags()
    {
        $dto = Mockery::mock(CreateProductDTO::class);

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Produto']);
        $dto->shouldReceive('getTags')->andReturn([1, 2]);

        $product = Mockery::mock(Product::class);
        $product->shouldReceive('tags')->andReturnSelf();
        $product->shouldReceive('sync')->with([1, 2])->once();
        $product->shouldReceive('load')->andReturn($product);

        $this->productRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($product);

        $this->tagRepository
            ->shouldReceive('findMany')
            ->with([1, 2])
            ->once()
            ->andReturn(collect([new Tag(), new Tag()]));

        $result = $this->service->create($dto);

        $this->assertSame($product, $result);
    }

    /** @test */
    public function it_throws_exception_when_tag_does_not_exist()
    {
        $this->expectException(ValidationException::class);

        $dto = Mockery::mock(CreateProductDTO::class);
        $dto->shouldReceive('toArray')->andReturn(['name' => 'Produto']);
        $dto->shouldReceive('getTags')->andReturn([1, 2]);

        $product = Mockery::mock(Product::class);

        $this->productRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($product);

        $this->tagRepository
            ->shouldReceive('findMany')
            ->with([1, 2])
            ->andReturn(collect([new Tag()]));

        $this->service->create($dto);
    }

    /** @test */
    public function it_updates_a_product()
    {
        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = '123';
        $dto->slug = 'new-slug';

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Atualizado']);
        $dto->shouldReceive('hasTags')->andReturn(false);

        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('find')->with('123')->andReturn($product);
        $this->productRepository->shouldReceive('slugExists')->with('new-slug', '123')->andReturn(false);
        $this->productRepository->shouldReceive('update')->with('123', ['name' => 'Atualizado']);
        $this->productRepository->shouldReceive('findWithRelations')->with('123')->andReturn($product);

        $result = $this->service->update($dto);

        $this->assertSame($product, $result);
    }

    /** @test */
    public function it_throws_validation_exception_when_product_not_found_on_update()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 999;
        $dto->slug = 'updated-product';

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(999)
            ->andReturn(null);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Produto não encontrado');

        $this->service->update($dto);
    }

    /** @test */
    public function it_throws_validation_exception_when_slug_already_exists_on_update()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 2;
        $dto->slug = 'existing-slug';

        $existingProduct = Mockery::mock(Product::class);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(2)
            ->andReturn($existingProduct);

        $this->productRepository
            ->shouldReceive('slugExists')
            ->with('existing-slug', 2)
            ->andReturn(true);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Produto não encontrado');

        $this->service->update($dto);
    }

    /** @test */
    public function it_updates_successfully_when_slug_is_null()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 1;
        $dto->slug = null;

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Updated Name']);
        $dto->shouldReceive('hasTags')->andReturn(false);

        $product = Mockery::mock(Product::class);
        $updatedProduct = Mockery::mock(Product::class);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($product);

        $this->productRepository
            ->shouldReceive('slugExists')
            ->never();

        $this->productRepository
            ->shouldReceive('update')
            ->with(1, ['name' => 'Updated Name'])
            ->andReturn(true);

        $this->productRepository
            ->shouldReceive('findWithRelations')
            ->with(1)
            ->andReturn($updatedProduct);

        $result = $this->service->update($dto);

        $this->assertSame($updatedProduct, $result);
    }

    /** @test */
    public function it_updates_successfully_when_slug_is_empty_string()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 1;
        $dto->slug = '';

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Updated Name']);
        $dto->shouldReceive('hasTags')->andReturn(false);

        $product = Mockery::mock(Product::class);
        $updatedProduct = Mockery::mock(Product::class);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($product);

        $this->productRepository
            ->shouldReceive('slugExists')
            ->never();

        $this->productRepository
            ->shouldReceive('update')
            ->with(1, ['name' => 'Updated Name'])
            ->andReturn(true);

        $this->productRepository
            ->shouldReceive('findWithRelations')
            ->with(1)
            ->andReturn($updatedProduct);

        $result = $this->service->update($dto);

        $this->assertSame($updatedProduct, $result);
    }

    /** @test */
    public function it_updates_successfully_with_new_unique_slug()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 1;
        $dto->slug = 'new-unique-slug';

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Updated Product']);
        $dto->shouldReceive('hasTags')->andReturn(false);

        $product = Mockery::mock(Product::class);
        $updatedProduct = Mockery::mock(Product::class);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($product);

        $this->productRepository
            ->shouldReceive('slugExists')
            ->with('new-unique-slug', 1)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('update')
            ->with(1, ['name' => 'Updated Product'])
            ->andReturn(true);

        $this->productRepository
            ->shouldReceive('findWithRelations')
            ->with(1)
            ->andReturn($updatedProduct);

        $result = $this->service->update($dto);

        $this->assertSame($updatedProduct, $result);
    }

    /** @test */
    public function it_updates_product_and_associates_tags()
    {
        $dto = Mockery::mock(UpdateProductDTO::class);
        $dto->id = 1;
        $dto->slug = 'product-slug';

        $dto->shouldReceive('toArray')->andReturn(['name' => 'Updated Product']);
        $dto->shouldReceive('hasTags')->andReturn(true);
        $dto->shouldReceive('getTags')->andReturn([1, 2]);

        $product = Mockery::mock(Product::class);
        $updatedProduct = Mockery::mock(Product::class);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->productRepository
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($product);

        $this->productRepository
            ->shouldReceive('slugExists')
            ->with('product-slug', 1)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('update')
            ->with(1, ['name' => 'Updated Product'])
            ->andReturn(true);

        $this->tagRepository
            ->shouldReceive('findMany')
            ->with([1, 2])
            ->andReturn(collect([new Tag(), new Tag()]));

        $product->shouldReceive('tags')->andReturnSelf();
        $product->shouldReceive('sync')->with([1, 2])->once();

        $this->productRepository
            ->shouldReceive('findWithRelations')
            ->with(1)
            ->andReturn($updatedProduct);

        $result = $this->service->update($dto);

        $this->assertSame($updatedProduct, $result);
    }

    /** @test */
    public function it_deletes_a_product()
    {
        $this->productRepository
            ->shouldReceive('delete')
            ->with('10')
            ->andReturn(true);

        $result = $this->service->delete('10');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_shows_a_product()
    {
        $product = new Product();

        $this->productRepository
            ->shouldReceive('find')
            ->with('55')
            ->andReturn($product);

        $result = $this->service->show('55');

        $this->assertSame($product, $result);
    }

    /** @test */
    public function it_lists_products()
    {
        $filters = ['page' => 1];

        $this->productRepository
            ->shouldReceive('paginate')
            ->with($filters)
            ->andReturn(['data' => []]);

        $result = $this->service->list($filters);

        $this->assertEquals(['data' => []], $result);
    }
}