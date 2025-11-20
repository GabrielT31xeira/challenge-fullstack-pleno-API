<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = new User();

        $this->assertEquals([
            'id',
            'name',
            'email',
            'password',
            'role',
        ], $user->getFillable());
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $user = new User();

        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $user = new User();

        $this->assertArrayHasKey('email_verified_at', $user->getCasts());
        $this->assertEquals('datetime', $user->getCasts()['email_verified_at']);
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $user = new User();

        $this->assertFalse($user->incrementing);
        $this->assertEquals('string', $user->getKeyType());
    }

    /** @test */
    public function it_can_check_if_admin_or_user()
    {
        $admin = new User(['role' => 'admin']);
        $user  = new User(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_has_orders_relation()
    {
        $user = new User();
        $this->assertInstanceOf(HasMany::class, $user->orders());
    }

    /** @test */
    public function it_has_carts_relation()
    {
        $user = new User();
        $this->assertInstanceOf(HasMany::class, $user->carts());
    }
}
