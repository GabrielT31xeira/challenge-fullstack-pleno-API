<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
