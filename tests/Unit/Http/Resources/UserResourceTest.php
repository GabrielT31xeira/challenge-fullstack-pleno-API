<?php

namespace Tests\Unit\Http\Resources\Auth;

use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_resource_returns_expected_array()
    {
        $user = User::factory()->create([
            'name' => 'Gabriel Carvalho',
            'email' => 'gabriel@example.com',
            'role' => 'user',
        ]);

        $resource = new UserResource($user);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('role', $array);

        $this->assertEquals($user->id, $array['id']);
        $this->assertEquals('Gabriel Carvalho', $array['name']);
        $this->assertEquals('gabriel@example.com', $array['email']);
        $this->assertEquals('user', $array['role']);
    }
}
