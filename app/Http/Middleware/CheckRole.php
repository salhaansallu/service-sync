<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required',
                'error' => 'FORBIDDEN'
            ], 403);
        }

        return $next($request);
    }
}
