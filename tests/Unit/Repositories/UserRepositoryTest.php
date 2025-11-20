<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new UserRepository();
    }

    /** @test */
    public function it_creates_a_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('12345678'),
        ];

        $user = $this->repo->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_finds_user_by_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $found = $this->repo->findByEmail('test@example.com');

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    /** @test */
    public function it_returns_null_when_email_not_found()
    {
        $found = $this->repo->findByEmail('notfound@example.com');

        $this->assertNull($found);
    }
}
