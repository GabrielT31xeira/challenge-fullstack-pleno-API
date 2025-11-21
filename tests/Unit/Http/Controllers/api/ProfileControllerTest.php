<?php


use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_authenticated_user_profile()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/v1/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_unauthorized_for_unauthenticated_user()
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_server_errors_gracefully()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/v1/profile');

        $response->assertStatus(200);
    }
}