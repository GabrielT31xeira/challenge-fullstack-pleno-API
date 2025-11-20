<?php

namespace Tests\Unit\DTO;

use App\DTO\Product\CreateProductDTO;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class CreateProductDTOTest extends TestCase
{
    public function test_it_creates_dto_from_request()
    {
        $request = Mockery::mock(\App\Http\Requests\Product\ProductStoreRequest::class)->makePartial();

        $request->name = 'Produto Teste';
        $request->price = '199.90';
        $request->category_id = 'abc123';
        $request->tags = ['tag1', 'tag2'];
        $request->quantity = '10';
        $request->description = 'Descrição teste';
        $request->slug = null;

        $request->shouldReceive('has')->with('cost_price')->andReturn(false);
        $request->shouldReceive('has')->with('min_quantity')->andReturn(false);
        $request->shouldReceive('get')->with('active', true)->andReturn(true);

        $dto = CreateProductDTO::fromRequest($request);

        $this->assertEquals('Produto Teste', $dto->name);
        $this->assertEquals(199.90, $dto->price);
        $this->assertEquals('abc123', $dto->categoryId);
        $this->assertEquals(['tag1', 'tag2'], $dto->tags);
        $this->assertEquals(10, $dto->quantity);
        $this->assertEquals('Descrição teste', $dto->description);
        $this->assertNotNull($dto->slug);
    }


    public function test_it_converts_to_array_correctly()
    {
        $dto = new CreateProductDTO(
            name: 'Produto Teste',
            price: 100,
            categoryId: 'cat01',
            tags: [],
            quantity: 5,
            slug: 'produto-teste',
            description: 'descrição',
            costPrice: 30,
            minQuantity: 2,
            active: true
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'name' => 'Produto Teste',
            'slug' => 'produto-teste',
            'description' => 'descrição',
            'price' => 100,
            'cost_price' => 30,
            'quantity' => 5,
            'min_quantity' => 2,
            'active' => true,
            'category_id' => 'cat01',
        ], $array);
    }

    public function test_it_throws_validation_exception_when_cost_price_is_too_high()
    {
        $dto = new CreateProductDTO(
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            tags: [],
            quantity: 10,
            slug: null,
            description: null,
            costPrice: 90,
            minQuantity: null,
            active: true
        );

        $this->expectException(ValidationException::class);

        $dto->validateBusinessRules();
    }

    public function test_it_throws_validation_exception_when_min_quantity_exceeds_quantity()
    {
        $dto = new CreateProductDTO(
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            tags: [],
            quantity: 5,
            slug: null,
            description: null,
            costPrice: 10,
            minQuantity: 10,
            active: true
        );

        $this->expectException(ValidationException::class);

        $dto->validateBusinessRules();
    }

    public function test_it_passes_business_rules()
    {
        $dto = new CreateProductDTO(
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            tags: [],
            quantity: 10,
            slug: null,
            description: null,
            costPrice: 50,
            minQuantity: 5,
            active: true
        );

        $dto->validateBusinessRules();

        $this->assertTrue(true);
    }
}
