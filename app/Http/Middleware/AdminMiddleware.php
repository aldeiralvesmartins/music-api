<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_admin) { // ajuste conforme seu campo real
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem acessar esta rota.'
            ], 403);
        }

        return $next($request);
    }
}
