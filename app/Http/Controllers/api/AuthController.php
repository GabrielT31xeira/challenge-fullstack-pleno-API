<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->toDTO());

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'data' => new UserResource($user)
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->toDTO());

        if ($result['error']) {
            return response()->json([
                'message' => $result['message']
            ], 401);
        }
        return response()->json([
            'message' => 'Login realizado correctamente',
            'token' => $result['token'],
            'data' => new UserResource($result['user'])
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logout realizado com sucesso'], 201);
    }
}
