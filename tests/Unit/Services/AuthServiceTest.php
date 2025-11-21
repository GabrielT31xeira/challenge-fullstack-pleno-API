<?php

namespace Tests\Unit\Services;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected UserRepository $userRepository;
    protected AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->service = new AuthService($this->userRepository);
    }

    /** @test */
    public function it_registers_a_new_user()
    {
        $dto = new RegisterDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'secret123'
        );

        $expectedUser = new \App\Models\User([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'John Doe'
                    && $data['email'] === 'john@example.com'
                    && Hash::check('secret123', $data['password'])
                    && $data['role'] === 'user';
            }))
            ->andReturn($expectedUser);

        $result = $this->service->register($dto);

        $this->assertSame($expectedUser, $result);
    }

    /** @test */
    public function it_returns_error_when_login_fails()
    {
        $dto = new LoginDTO(
            email: 'john@example.com',
            password: 'wrongpass'
        );

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with('john@example.com')
            ->andReturn(null);

        $result = $this->service->login($dto);

        $this->assertTrue($result['error']);
    }

    /** @test */
    public function it_logs_out_user()
    {
        $mockTokens = Mockery::mock();
        $mockTokens->shouldReceive('delete')->once();

        $mockUser = Mockery::mock();
        $mockUser->shouldReceive('tokens')
            ->andReturn($mockTokens);

        $this->service->logout($mockUser);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_logs_in_successfully()
    {
        $user = new User([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $user = Mockery::mock($user)->makePartial();
        $user->shouldReceive('createToken')
            ->once()
            ->with('api_token')
            ->andReturn((object) ['plainTextToken' => 'mocked-token-here']);

        $dto = new LoginDTO(
            email: 'john@example.com',
            password: 'secret123'
        );

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with('john@example.com')
            ->andReturn($user);

        $result = $this->service->login($dto);

        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals('mocked-token-here', $result['token']);
        $this->assertSame($user, $result['user']);
    }
}
