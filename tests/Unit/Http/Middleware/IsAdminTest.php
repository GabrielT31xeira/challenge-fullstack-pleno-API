<?php

namespace tests\Unit\Middleware;

use Tests\TestCase;
use App\Http\Middleware\IsAdmin;
use App\Models\User;
use Illuminate\Http\Request;

class IsAdminTest extends TestCase
{
    /** @test */
    public function it_denies_access_if_user_not_authenticated()
    {
        $middleware = new IsAdmin();

        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function () {});

        $this->assertEquals(401, $response->status());
        $this->assertEquals(
            ['message' => 'Usuário não autenticado.'],
            $response->getData(true)
        );
    }

    /** @test */
    public function it_denies_access_if_user_is_not_admin()
    {
        $user = User::factory()->make(['role' => 'user']);

        $this->actingAs($user, 'sanctum');

        $middleware = new IsAdmin();
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function () {});

        $this->assertEquals(403, $response->status());
        $this->assertEquals(
            ['message' => 'Acesso negado. Apenas administradores podem realizar esta ação.'],
            $response->getData(true)
        );
    }

    /** @test */
    public function it_allows_access_if_user_is_admin()
    {
        $user = User::factory()->make(['role' => 'admin']);

        $this->actingAs($user, 'sanctum');

        $middleware = new IsAdmin();
        $request = Request::create('/test', 'GET');

        $called = false;

        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;
            return response('next called');
        });

        $this->assertTrue($called);
        $this->assertEquals('next called', $response->getContent());
    }
}
