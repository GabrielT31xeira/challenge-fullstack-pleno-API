<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {

        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado.',
            ], 401);
        }

        if ($user->role === 'user') {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem realizar esta ação.',
            ], 403);
        }

        return $next($request);
    }
}
