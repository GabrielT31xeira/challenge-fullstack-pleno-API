<?php

namespace Tests\Integration\Validations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class OrderValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_validation()
    {
        $user = User::factory()->create();

        $payload = [
            'status' => 'invalid-status'
        ];

        $this->actingAs($user)
            ->patchJson('/api/orders/status/123', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
