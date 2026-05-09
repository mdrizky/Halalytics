<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\JsonResponse;

class SecurityController extends Controller
{
    /**
     * 🔒 Secure API Key Provider
     * Provides API keys to authenticated mobile clients only
     */
    public function getApiKeys(Request $request): JsonResponse
    {
        // Rate limiting: 5 requests per minute per IP
        $executed = RateLimiter::attempt(
            'api-keys:'.$request->ip(),
            $perMinute = 5,
            function() {
                // User is authenticated and has permission
                if (!auth()->check()) {
                    abort(401, 'Authentication required');
                }
                
                // Only allow verified users
                if (!auth()->user()->email_verified_at) {
                    abort(403, 'Email verification required');
                }
            }
        );

        if (!$executed) {
            return response()->json([
                'error' => 'Too many requests',
                'message' => 'Please try again later',
                'retry_after' => RateLimiter::availableIn('api-keys:'.$request->ip())
            ], 429);
        }

        // Cache API keys for 5 minutes to reduce database load
        $cacheKey = 'api-keys:'.auth()->id();
        $apiKeys = Cache::remember($cacheKey, 300, function () {
            return [
                'gemini_api_key' => config('services.gemini.api_key'),
                'newsdata_api_key' => config('services.newsdata.api_key'),
                'anthropic_api_key' => config('services.anthropic.api_key'),
                'unsplash_api_key' => config('services.unsplash.api_key'),
                'reverb_app_key' => config('broadcasting.reverb.app_key'),
                'reverb_url' => config('broadcasting.reverb.url'),
                'api_cert_pin' => config('app.api_cert_pin'),
                'environment' => config('app.env'),
                'debug_mode' => config('app.debug'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $apiKeys,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * 🛡️ Validate API Request Security
     * Checks if request meets security requirements
     */
    public function validateRequest(Request $request): JsonResponse
    {
        $securityChecks = [
            'authenticated' => auth()->check(),
            'email_verified' => auth()->check() && auth()->user()->email_verified_at,
            'rate_limit_ok' => !RateLimiter::tooManyAttempts('security:'.$request->ip(), 60),
            'user_active' => auth()->check() && auth()->user()->active,
            'session_valid' => auth()->check() && auth()->user()->last_login && 
                             auth()->user()->last_login->diffInDays(now()) < 30,
        ];

        $securityScore = array_sum($securityChecks) / count($securityChecks);

        return response()->json([
            'security_score' => $securityScore,
            'checks' => $securityChecks,
            'recommendations' => $this->getSecurityRecommendations($securityChecks),
        ]);
    }

    /**
     * 📊 Get security recommendations based on checks
     */
    private function getSecurityRecommendations(array $checks): array
    {
        $recommendations = [];

        if (!$checks['authenticated']) {
            $recommendations[] = 'Please login to access this feature';
        }

        if (!$checks['email_verified']) {
            $recommendations[] = 'Verify your email address for full access';
        }

        if (!$checks['user_active']) {
            $recommendations[] = 'Your account is inactive. Please contact support';
        }

        if (!$checks['session_valid']) {
            $recommendations[] = 'Please login again to refresh your session';
        }

        return $recommendations;
    }

    /**
     * 🔄 Refresh API Keys (for security rotation)
     */
    public function refreshApiKeys(Request $request): JsonResponse
    {
        // Clear cache for this user
        Cache::forget('api-keys:'.auth()->id());

        // Log the refresh for security audit
        activity('api_keys_refreshed')
            ->by(auth()->user())
            ->withProperties(['ip' => $request->ip()])
            ->log('API keys refreshed by user');

        return response()->json([
            'success' => true,
            'message' => 'API keys refreshed successfully',
        ]);
    }
}
