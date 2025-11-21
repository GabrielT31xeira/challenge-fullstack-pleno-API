<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\Auth\RegisterRequest;
use App\DTO\Auth\RegisterDTO;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequestTest extends TestCase
{
    /** @test */
    public function it_has_correct_validation_rules()
    {
        $request = new RegisterRequest();

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertEquals('required|string|max:255', $rules['name']);
        $this->assertEquals('required|email|unique:users,email', $rules['email']);
        $this->assertEquals('required|min:6', $rules['password']);
    }

    /** @test */
    public function it_has_custom_error_messages()
    {
        $request = new RegisterRequest();
        $messages = $request->messages();

        $this->assertEquals('O campo nome é obrigatório.', $messages['name.required']);
        $this->assertEquals('O nome deve ser um texto válido.', $messages['name.string']);
        $this->assertEquals('O nome não pode ultrapassar 255 caracteres.', $messages['name.max']);
        $this->assertEquals('O campo e-mail é obrigatório.', $messages['email.required']);
        $this->assertEquals('Informe um endereço de e-mail válido.', $messages['email.email']);
        $this->assertEquals('Este e-mail já está cadastrado.', $messages['email.unique']);
        $this->assertEquals('O campo senha é obrigatório.', $messages['password.required']);
        $this->assertEquals('A senha deve conter pelo menos 6 caracteres.', $messages['password.min']);
    }

    /** @test */
    public function it_converts_request_to_dto()
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $dto = $request->toDTO();

        $this->assertInstanceOf(RegisterDTO::class, $dto);
        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123'
        ];

        $request = new RegisterRequest();

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->all();
        $this->assertContains('O campo nome é obrigatório.', $errors);
        $this->assertContains('Informe um endereço de e-mail válido.', $errors);
        $this->assertContains('A senha deve conter pelo menos 6 caracteres.', $errors);
    }

    /** @test */
    public function it_returns_custom_error_response_format_on_validation_failure()
    {
        // Mock da ApiResponse ANTES de instanciar o request
        $mockResponse = new \Illuminate\Http\JsonResponse([
            'success' => false,
            'message' => 'Os seguintes erros foram encontrados',
            'errors' => ['Test error 1', 'Test error 2']
        ], 422);

        $apiResponseMock = \Mockery::mock('overload:App\Http\Responses\ApiResponse');
        $apiResponseMock->shouldReceive('error')
            ->once()
            ->with('Os seguintes erros foram encontrados', ['Test error 1', 'Test error 2'])
            ->andReturn($mockResponse);

        $request = new RegisterRequest();

        // Criar validator mock
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('errors')
            ->andReturn(new \Illuminate\Support\MessageBag([
                'field1' => ['Test error 1'],
                'field2' => ['Test error 2']
            ]));

        // Usar Reflection para acessar o método protegido
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('failedValidation');
        $method->setAccessible(true);

        $this->expectException(HttpResponseException::class);

        $method->invoke($request, $validatorMock);
    }

    /** @test */
    public function it_returns_custom_error_response_with_specific_errors()
    {
        $request = new RegisterRequest();

        $invalidData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'short',
        ];

        $validator = Validator::make($invalidData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ], $request->messages());

        $validator->errors()->add('email', 'Este e-mail já está cadastrado.');
        $validator->errors()->add('password', 'A senha deve conter pelo menos 6 caracteres.');

        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('failedValidation');
        $method->setAccessible(true);

        try {
            $method->invoke($request, $validator);
            $this->fail('Expected HttpResponseException was not thrown.');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $responseData = json_decode($response->getContent(), true);

            $this->assertFalse($responseData['success']);
            $this->assertEquals('Os seguintes erros foram encontrados', $responseData['message']);
            $this->assertContains('Este e-mail já está cadastrado.', $responseData['errors']);
            $this->assertContains('A senha deve conter pelo menos 6 caracteres.', $responseData['errors']);
        }
    }
}