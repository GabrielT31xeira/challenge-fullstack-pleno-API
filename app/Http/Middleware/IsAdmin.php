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
                'success' => false,
                'message' => 'Usuário não autenticado',
                'errors' => []
            ], 401);
        }

        if ($user->role === 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem realizar esta ação',
                'errors' => []
            ], 403);
        }

        return $next($request);
    }
}
