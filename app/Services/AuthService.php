<?php

namespace App\Services;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $users
    ) {}

    public function register(RegisterDTO $data)
    {
        return $this->users->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'role' => 'user',
        ]);
    }

    public function login(LoginDTO $data)
    {
        $user = $this->users->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            abort(401, 'Credenciales incorrectas');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }
}
