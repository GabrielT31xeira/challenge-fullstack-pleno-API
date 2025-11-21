<?php

namespace Tests\Integration\Validations;

use App\Models\Order;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class OrderValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_validation()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);
        $order = Order::factory()->create();
        $payload = [
            'status' => 'invalid-status'
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->putJson('/api/v1/orders/'. $order->id.'/status', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
