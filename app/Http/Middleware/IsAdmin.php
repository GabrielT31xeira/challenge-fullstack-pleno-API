<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {

        if (!PersonalAccessToken::findToken($request->bearerToken()) || !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
