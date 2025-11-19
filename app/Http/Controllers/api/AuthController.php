<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->toDTO());

            return ApiResponse::success(new UserResource($user));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }

    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->toDTO());

            if ($result['error']) {
                return ApiResponse::error(
                    "Erro nas credenciais enviadas, tente novamente",
                );
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token']
                ]
            ]);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage()
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return ApiResponse::success();
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }
}
