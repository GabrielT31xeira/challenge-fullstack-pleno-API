<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\Auth\LoginRequest;
use App\DTO\Auth\LoginDTO;
use Illuminate\Support\Facades\Validator;

class LoginRequestTest extends TestCase
{
    /** @test */
    public function it_has_correct_validation_rules()
    {
        $request = new LoginRequest();

        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|email', $rules['email']);
        $this->assertEquals('required|string', $rules['password']);
    }

    /** @test */
    public function it_has_custom_error_messages()
    {
        $request = new LoginRequest();

        $messages = $request->messages();

        $this->assertEquals('O campo e-mail é obrigatório.', $messages['email.required']);
        $this->assertEquals('Informe um endereço de e-mail válido.', $messages['email.email']);
        $this->assertEquals('O campo senha é obrigatório.', $messages['password.required']);
        $this->assertEquals('A senha deve ser um texto válido.', $messages['password.string']);
    }

    /** @test */
    public function it_converts_request_to_dto()
    {
        $request = new LoginRequest();

        $request->merge([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $dto = $request->toDTO();

        $this->assertInstanceOf(LoginDTO::class, $dto);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => '',
        ];

        $request = new LoginRequest();

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
        $this->assertEquals(
            'Informe um endereço de e-mail válido.',
            $validator->errors()->first('email')
        );
        $this->assertEquals(
            'O campo senha é obrigatório.',
            $validator->errors()->first('password')
        );
    }

    /** @test */
    public function it_passes_validation_with_valid_data()
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ];

        $request = new LoginRequest();

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }
}
