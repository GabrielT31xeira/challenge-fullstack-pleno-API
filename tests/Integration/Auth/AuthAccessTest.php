<?php

namespace Tests\Integration\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_order()
    {
        $this->postJson('/api/orders', [])
            ->assertStatus(401);
    }
}
