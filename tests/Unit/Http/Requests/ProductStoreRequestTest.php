<?php

namespace Tests\Unit\Http\Requests;

use App\Models\Category;
use Tests\TestCase;
use App\Http\Requests\Product\ProductStoreRequest;
use Illuminate\Support\Facades\Validator;

class ProductStoreRequestTest extends TestCase
{
    public function test_rules_are_correct()
    {
        $request = new ProductStoreRequest();

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('price', $rules);
        $this->assertArrayHasKey('category_id', $rules);
        $this->assertArrayHasKey('tags', $rules);
    }

    public function test_messages_are_correct()
    {
        $request = new ProductStoreRequest();
        $messages = $request->messages();

        $this->assertEquals('O nome é obrigatório.', $messages['name.required']);
        $this->assertEquals('Já existe um produto com este nome.', $messages['name.unique']);
        $this->assertEquals('O preço é obrigatório.', $messages['price.required']);
        $this->assertEquals('O custo deve ser menor que o preço de venda.', $messages['cost_price.lt']);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $data = [
            'name' => '',
            'price' => 0,
            'quantity' => -5,
            'category_id' => 'fake',
            'tags' => 'not-array'
        ];

        $request = new ProductStoreRequest();

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());

        $errors = $validator->errors()->all();

        $this->assertContains('O nome é obrigatório.', $errors);
        $this->assertContains('O preço deve ser maior que zero.', $errors);
        $this->assertContains('A quantidade não pode ser negativa.', $errors);
        $this->assertContains('A categoria informada não existe.', $errors);
        $this->assertContains('As tags devem ser um array.', $errors);
    }

    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'name' => 'Produto Teste',
            'price' => 100,
            'quantity' => 10,
            'category_id' => 'fake',
            'tags' => ['fake1', 'fake2'],
            'active' => true
        ];

        $request = new ProductStoreRequest();

        $rules = $request->rules();

        unset($rules['category_id'], $rules['tags.*']);

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_authorizes_all_users()
    {
        $request = new ProductStoreRequest();

        $this->assertTrue($request->authorize());
    }
}
