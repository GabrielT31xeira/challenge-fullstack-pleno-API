<?php

namespace Tests\Unit\DTO;

use App\DTO\Product\UpdateProductDTO;
use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class UpdateProductDTOTest extends TestCase
{
    public function test_it_creates_dto_from_request()
    {
        $request = Mockery::mock(ProductUpdateRequest::class)->makePartial();

        $request->name = 'Produto Atualizado';
        $request->price = '150.40';
        $request->category_id = 'cat123';
        $request->quantity = '8';
        $request->description = 'Descrição atualizada';
        $request->slug = null;
        $request->tags = ['a', 'b'];

        $request->shouldReceive('has')->with('cost_price')->andReturn(false);
        $request->shouldReceive('has')->with('min_quantity')->andReturn(false);
        $request->shouldReceive('has')->with('active')->andReturn(false);

        $dto = UpdateProductDTO::fromRequest($request, 'prod001');

        $this->assertEquals('prod001', $dto->id);
        $this->assertEquals('Produto Atualizado', $dto->name);
        $this->assertEquals(150.40, $dto->price);
        $this->assertEquals('cat123', $dto->categoryId);
        $this->assertEquals(8, $dto->quantity);
        $this->assertEquals(['a', 'b'], $dto->tags);
        $this->assertEquals('Descrição atualizada', $dto->description);
        $this->assertNotNull($dto->slug);
    }

    public function test_to_array_filters_null_values()
    {
        $dto = new UpdateProductDTO(
            id: '1',
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            quantity: 5,
            slug: null,
            description: null,
            costPrice: null,
            minQuantity: null,
            active: null,
            tags: []
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'name' => 'Produto',
            'price' => 100,
            'quantity' => 5,
            'category_id' => 'cat',
        ], $array);
    }

    public function test_business_rule_fails_when_cost_price_is_too_high()
    {
        $dto = new UpdateProductDTO(
            id: '1',
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            quantity: 10,
            costPrice: 90
        );

        $this->expectException(ValidationException::class);
        $dto->validateBusinessRules();
    }

    public function test_business_rule_fails_when_min_quantity_greater_than_quantity()
    {
        $dto = new UpdateProductDTO(
            id: '1',
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            quantity: 5,
            minQuantity: 10
        );

        $this->expectException(ValidationException::class);
        $dto->validateBusinessRules();
    }

    public function test_business_rule_fails_when_more_than_10_tags()
    {
        $dto = new UpdateProductDTO(
            id: '1',
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            quantity: 10,
            tags: range(1, 11)
        );

        $this->expectException(ValidationException::class);
        $dto->validateBusinessRules();
    }

    public function test_business_rules_pass_with_valid_data()
    {
        $dto = new UpdateProductDTO(
            id: '1',
            name: 'Produto',
            price: 100,
            categoryId: 'cat',
            quantity: 10,
            costPrice: 70,
            minQuantity: 5,
            tags: ['tag1', 'tag2']
        );

        $this->assertNull($dto->validateBusinessRules());
    }
}
