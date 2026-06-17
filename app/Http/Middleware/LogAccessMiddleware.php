<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;

class LogAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user() && $this->shouldLogAccess($request)) {
            try {
                $this->logAccess($request, $response);
            } catch (\Exception $e) {
                \Log::error('Access logging failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    private function shouldLogAccess(Request $request): bool
    {
        $loggedRoutes = [
            'api/nutrition/consultations',
            'api/nutritionist/dashboard',
            'api/nutritionist/consultations',
            'api/users',
            'api/health',
            'api/medical-records',
        ];

        $path = $request->path();
        foreach ($loggedRoutes as $route) {
            if (strpos($path, $route) !== false) {
                return true;
            }
        }

        return false;
    }

    private function logAccess(Request $request, $response): void
    {
        $user = $request->user();
        $resourceType = $this->getResourceType($request);
        $resourceId = $this->getResourceId($request);
        $action = $request->method() . ' ' . $request->path();
        $result = $response->status() < 400 ? 'success' : 'denied';

        AccessLog::log(
            userId: $user?->id_user,
            resourceType: $resourceType,
            resourceId: $resourceId,
            action: $action,
            result: $result,
            details: [
                'status_code' => $response->status(),
                'user_role' => $user?->role ?? 'guest',
                'query_params' => $request->query(),
            ]
        );
    }

    private function getResourceType(Request $request): string
    {
        if (strpos($request->path(), 'consultation') !== false) {
            return 'nutrition_consultation';
        } elseif (strpos($request->path(), 'health') !== false) {
            return 'health_record';
        } elseif (strpos($request->path(), 'medical') !== false) {
            return 'medical_record';
        } elseif (strpos($request->path(), 'user') !== false) {
            return 'user_data';
        }

        return 'unknown';
    }

    private function getResourceId(Request $request): ?int
    {
        if (preg_match('/\/(\d+)/', $request->path(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
