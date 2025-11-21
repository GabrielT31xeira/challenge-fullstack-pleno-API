<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Cart\UpdateCartRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateCartRequestTest extends TestCase
{
    public function test_rules_exist()
    {
        $request = new UpdateCartRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('quantity', $rules);
    }

    public function test_messages_exist()
    {
        $request = new UpdateCartRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('quantity.required', $messages);
        $this->assertArrayHasKey('quantity.integer', $messages);
        $this->assertArrayHasKey('quantity.min', $messages);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $data = [
            'quantity' => 0,
        ];

        $request = new UpdateCartRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('quantity', $errors);
        $this->assertEquals('A quantidade mínima permitida é 1.', $errors['quantity'][0]);
    }

    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'quantity' => 5,
        ];

        $request = new UpdateCartRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_authorizes_all_users()
    {
        $request = new UpdateCartRequest();

        $this->assertTrue($request->authorize());
    }
}
