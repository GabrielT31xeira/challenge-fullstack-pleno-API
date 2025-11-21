<?php

namespace App\Services;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected UserRepository $usersRepository
    ) {}

    public function register(RegisterDTO $data)
    {
        return $this->usersRepository->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'role' => 'user',
        ]);
    }

    public function login(LoginDTO $data)
    {
        $user = $this->usersRepository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            return [
                'error' => true,
            ];
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'error' => false,
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }
}
