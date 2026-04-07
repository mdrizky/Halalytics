<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
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
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            || ($user->role ?? null) === 'admin';

        if ($isAdmin) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Admin access required.',
            'data' => null,
        ], 403);
    }
}
