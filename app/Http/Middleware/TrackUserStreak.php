<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrackUserStreak
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $today = Carbon::today();
            $lastActive = $user->last_active_date ? Carbon::parse($user->last_active_date)->startOfDay() : null;

            if (!$lastActive || !$lastActive->isSameDay($today)) {
                if (!$lastActive) {
                    $user->current_streak = 1;
                    $user->longest_streak = 1;
                } elseif ($lastActive->diffInDays($today) == 1) {
                    $user->current_streak += 1;
                    if ($user->current_streak > $user->longest_streak) {
                        $user->longest_streak = $user->current_streak;
                    }
                } elseif ($lastActive->diffInDays($today) > 1) {
                    $user->current_streak = 1;
                }
                
                $user->last_active_date = $today->toDateString();
                $user->save();
            }
        }

        return $next($request);
    }
}
