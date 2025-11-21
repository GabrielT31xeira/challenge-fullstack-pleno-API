<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductUpdateRequestTest extends TestCase
{
    public function test_validation_rules_exist()
    {
        $request = new ProductUpdateRequest();

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('price', $rules);
        $this->assertArrayHasKey('quantity', $rules);
        $this->assertArrayHasKey('category_id', $rules);
    }

    public function test_validation_messages_exist()
    {
        $request = new ProductUpdateRequest();

        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('price.required', $messages);
        $this->assertArrayHasKey('quantity.min', $messages);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $data = [
            'name' => '',
            'price' => 0,
            'quantity' => -1,
            'category_id' => 'fake',
        ];

        $request = new ProductUpdateRequest();

        $rules = $request->rules();
        unset($rules['category_id'], $rules['tags.*']);

        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());
    }

    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'name' => 'Produto Teste',
            'price' => 100,
            'quantity' => 10,
            'category_id' => 'fake',
            'active' => true,
        ];

        $request = new ProductUpdateRequest();

        $rules = $request->rules();
        unset($rules['category_id'], $rules['tags.*']);

        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_authorizes_all_users()
    {
        $request = new ProductUpdateRequest();

        $this->assertTrue($request->authorize());
    }
}
