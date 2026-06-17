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

            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();

        // Map role aliases so ahli_gizi / expert / nutritionist all pass each other
        $allowedRoles = match ($role) {
            'ahli_gizi' => ['ahli_gizi', 'ahli gizi', 'expert', 'nutritionist'],
            'expert' => ['ahli_gizi', 'ahli gizi', 'expert', 'nutritionist'],
            'nutritionist' => ['ahli_gizi', 'ahli gizi', 'expert', 'nutritionist'],
            'admin' => ['admin', 'superadmin'],
            'superadmin' => ['admin', 'superadmin'],
            default => [$role],
        };

        $hasRole = method_exists($user, 'hasRole') && collect($allowedRoles)->contains(fn($r) => $user->hasRole($r));
        $legacyRoleMatch = in_array($user->role ?? null, $allowedRoles);

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
