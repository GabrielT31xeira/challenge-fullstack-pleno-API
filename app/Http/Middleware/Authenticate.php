<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'message' => 'Token de acesso não fornecido',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'message' => 'Token inválido ou expirado',
            ], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'message' => 'Token expirado',
            ], 401);
        }

        $user = $accessToken->tokenable;
        auth()->setUser($user);

        return $next($request);
    }
}
