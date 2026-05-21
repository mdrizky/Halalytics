<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * 🛡️ Enterprise Rate Limiting Middleware
 * Prevents brute force attacks and API abuse
 */
class EnterpriseRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip rate limiting for specific IPs (internal, monitoring)
        if ($this->isWhitelisted($request)) {
            return $next($request);
        }

        // Different rate limits for different endpoints
        if ($this->isAuthEndpoint($request)) {
            return $this->checkAuthRateLimit($request);
        }

        if ($this->isSensitiveEndpoint($request)) {
            return $this->checkSensitiveRateLimit($request);
        }

        if ($this->isApiEndpoint($request)) {
            return $this->checkApiRateLimit($request);
        }

        return $next($request);
    }

    /**
     * Check rate limit for auth endpoints (login, register, password reset)
     */
    private function checkAuthRateLimit(Request $request): Response|null
    {
        $identifier = $request->input('email') ?? $request->input('login') ?? $request->ip();
        $key = "auth:{$identifier}";

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Too many auth attempts. Please try again in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, 60); // 60 second window

        return null;
    }

    /**
     * Check rate limit for sensitive endpoints (profile update, password change)
     */
    private function checkSensitiveRateLimit(Request $request): Response|null
    {
        $key = "sensitive:{$request->user()?->id}:{$request->path()}";

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, 300); // 5 minute window

        return null;
    }

    /**
     * Check general API rate limit
     */
    private function checkApiRateLimit(Request $request): Response|null
    {
        $userId = $request->user()?->id ?? $request->ip();
        $key = "api:{$userId}";

        if (RateLimiter::tooManyAttempts($key, 100)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please slow down.',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, 60); // 1 minute window

        return null;
    }

    /**
     * Check if endpoint is auth-related
     */
    private function isAuthEndpoint(Request $request): bool
    {
        return $request->is('api/login', 'api/register', 'api/forgot-password', 'api/auth/*');
    }

    /**
     * Check if endpoint is sensitive
     */
    private function isSensitiveEndpoint(Request $request): bool
    {
        return $request->is(
            'api/user/change-password',
            'api/user/profile',
            'api/admin/*',
            'api/nutritionist/*'
        );
    }

    /**
     * Check if endpoint is API endpoint
     */
    private function isApiEndpoint(Request $request): bool
    {
        return $request->is('api/*');
    }

    /**
     * Check if IP is whitelisted
     */
    private function isWhitelisted(Request $request): bool
    {
        $whitelist = config('services.rate_limit.whitelist', [
            '127.0.0.1',
            '::1',
        ]);

        return in_array($request->ip(), $whitelist);
    }
}
