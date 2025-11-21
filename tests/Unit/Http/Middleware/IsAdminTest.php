<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Models\User;

class IsAdminTest extends TestCase
{
    /** @test */
    public function it_blocks_non_authenticated_users()
    {
        $response = $this->getJson('api/admin-route');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Usuário não autenticado',
                'errors' => []
            ]);
    }

    /** @test */
    public function it_blocks_regular_users()
    {
        $user = User::factory()->create(['role' => 'user']);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('api/admin-route');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem realizar esta ação',
                'errors' => []
            ]);
    }

    /** @test */
    public function it_allows_admin_users()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('api/admin-route');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_blocks_other_roles()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('api/admin-route');
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem realizar esta ação',
                'errors' => []
            ]);
    }
}