<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null,
                ], 401);
            }

            return redirect('/')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();

        $hasRole = method_exists($user, 'hasRole') && $user->hasRole($role);
        $legacyRoleMatch = ($user->role ?? null) === $role;

        if (! $hasRole && ! $legacyRoleMatch) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                    'data' => null,
                ], 403);
            }

            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
