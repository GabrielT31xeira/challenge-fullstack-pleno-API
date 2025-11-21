<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Http\Middleware\VerifyToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class VerifyTokenTest extends TestCase
{
    /** @test */
    public function it_returns_500_when_token_validation_throws_exception()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        Auth::shouldReceive('guard')
            ->with('sanctum')
            ->once()
            ->andThrow(new Exception('Erro inesperado na validação'));

        $middleware = new VerifyToken();

        $response = $middleware->handle($request, function () {
            $this->fail('Next closure should not be called when exception is thrown');
        });

        $this->assertEquals(500, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Erro ao validar token', $responseData['message']);
        $this->assertEquals('Erro inesperado na validação', $responseData['errors']);
    }

    /** @test */
    public function it_returns_500_with_different_exception_messages()
    {
        $exceptionMessages = [
            'Database connection failed',
            'Token service unavailable',
            'Unexpected validation error'
        ];

        foreach ($exceptionMessages as $message) {
            $request = Request::create('/test', 'GET');
            $request->headers->set('Authorization', 'Bearer valid-token');

            Auth::shouldReceive('guard')
                ->with('sanctum')
                ->once()
                ->andThrow(new Exception($message));

            $middleware = new VerifyToken();

            $response = $middleware->handle($request, function () {});

            $this->assertEquals(500, $response->getStatusCode());

            $responseData = $response->getData(true);
            $this->assertFalse($responseData['success']);
            $this->assertEquals('Erro ao validar token', $responseData['message']);
            $this->assertEquals($message, $responseData['errors']);
        }
    }

    /** @test */
    public function it_handles_different_types_of_exceptions()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        Auth::shouldReceive('guard')
            ->with('sanctum')
            ->once()
            ->andThrow(new \RuntimeException('Runtime error occurred'));

        $middleware = new VerifyToken();

        $response = $middleware->handle($request, function () {});

        $this->assertEquals(500, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Erro ao validar token', $responseData['message']);
        $this->assertEquals('Runtime error occurred', $responseData['errors']);
    }

    /** @test */
    public function it_includes_exception_message_in_errors_array()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        $exceptionMessage = 'Custom validation exception message';

        Auth::shouldReceive('guard')
            ->with('sanctum')
            ->once()
            ->andThrow(new Exception($exceptionMessage));

        $middleware = new VerifyToken();

        $response = $middleware->handle($request, function () {});

        $responseData = $response->getData(true);

        $this->assertEquals($exceptionMessage, $responseData['errors']);
        $this->assertIsString($responseData['errors']);
    }

    /** @test */
    public function exception_scenario_does_not_call_next_middleware()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        Auth::shouldReceive('guard')
            ->with('sanctum')
            ->once()
            ->andThrow(new Exception('Test exception'));

        $middleware = new VerifyToken();

        $nextCalled = false;
        $response = $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['success' => true]);
        });

        $this->assertFalse($nextCalled);
        $this->assertEquals(500, $response->getStatusCode());
    }
}