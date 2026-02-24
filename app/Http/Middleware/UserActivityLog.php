<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $actionName = 'UNKNOWN_ACTION'): Response
    {
        $response = $next($request);

        // Only log if successful or specific error codes? For monitoring we log all.
        // We do this AFTER the request is handled to not block response time significantly,
        // or use Terminable Middleware. For simplicity here:
        
        try {
            if (Auth::check()) {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => $actionName,
                    'description' => $this->getDescription($request, $actionName),
                    'ip_address' => $request->ip(),
                    'device_info' => $request->header('User-Agent'),
                    'is_risk_detected' => false, // Logic to detect risk can be added here or in controller
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Trigger Pusher/Reverb Event here for Real-time Admin Dashboard
                // event(new \App\Events\UserActivityEvent(Auth::user(), $actionName));
            }
        } catch (\Exception $e) {
            // Do not fail the request if logging fails
        }

        return $response;
    }

    private function getDescription(Request $request, string $action) {
        if ($action === 'SCAN_FOOD') {
            return 'User scanned a meal.';
        }
        if ($action === 'ADD_MEDICINE') {
            return 'User added medicine: ' . $request->input('name', 'Unknown');
        }
        return 'User performed ' . $action;
    }
}
