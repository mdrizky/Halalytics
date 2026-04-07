<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }

        $user = Auth::user();
        $isExpert = (method_exists($user, 'hasRole') && $user->hasRole('expert'))
            || ($user->role ?? null) === 'expert';

        if (! $isExpert) {
            return response()->json([
                'success' => false,
                'message' => 'Akses khusus pakar.',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
