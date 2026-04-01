<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    /** GET /api/user/points — Get current user's total points + level info. */
    public function myPoints()
    {
        $user  = Auth::user();
        $total = UserPoint::totalForUser($user->id_user);
        $level = UserPoint::levelForPoints($total);

        $progress = $level['next']
            ? round(($total - $level['min']) / ($level['next'] - $level['min']) * 100, 1)
            : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'total_points'     => $total,
                'level'            => $level['name'],
                'badge'            => $level['badge'],
                'progress_percent' => $progress,
                'points_to_next'   => $level['next'] ? max(0, $level['next'] - $total) : 0,
                'next_level'       => $level['next'] ? UserPoint::levelForPoints($level['next'])['name'] : null,
            ],
        ]);
    }

    /** GET /api/user/points/history — Points history with pagination. */
    public function history(Request $request)
    {
        $user = Auth::user();
        $history = UserPoint::forUser($user->id_user)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }
}
