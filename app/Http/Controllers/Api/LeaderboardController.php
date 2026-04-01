<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /** GET /api/leaderboard — Top contributors this month. */
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly'); // monthly | all_time
        $limit  = min((int) $request->get('limit', 10), 50);

        $query = DB::table('user_points')
            ->join('users', 'user_points.user_id', '=', 'users.id_user')
            ->select(
                'users.id_user',
                'users.username',
                'users.full_name',
                DB::raw('SUM(user_points.points) as total_points'),
                DB::raw('COUNT(user_points.id) as total_actions')
            )
            ->groupBy('users.id_user', 'users.username', 'users.full_name');

        if ($period === 'monthly') {
            $query->where('user_points.created_at', '>=', now()->startOfMonth());
        }

        $leaders = $query->orderByDesc('total_points')
            ->limit($limit)
            ->get()
            ->map(function ($row, $index) {
                $level = UserPoint::levelForPoints((int) $row->total_points);
                return [
                    'rank'         => $index + 1,
                    'user_id'      => $row->id_user,
                    'username'     => $row->username,
                    'full_name'    => $row->full_name,
                    'total_points' => (int) $row->total_points,
                    'total_actions'=> (int) $row->total_actions,
                    'level'        => $level['name'],
                    'badge'        => $level['badge'],
                ];
            });

        return response()->json([
            'success' => true,
            'period'  => $period,
            'data'    => $leaders,
        ]);
    }

    /** GET /api/user/rank — Current user's rank this month. */
    public function myRank(Request $request)
    {
        $user   = Auth::user();
        $period = $request->get('period', 'monthly');

        $myTotal = UserPoint::forUser($user->id_user);
        if ($period === 'monthly') {
            $myTotal = $myTotal->thisMonth();
        }
        $myTotal = (int) $myTotal->sum('points');

        // Count users with more points
        $rankQuery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as total'))
            ->groupBy('user_id');

        if ($period === 'monthly') {
            $rankQuery->where('created_at', '>=', now()->startOfMonth());
        }

        $rank = DB::table(DB::raw("({$rankQuery->toSql()}) as sub"))
            ->mergeBindings($rankQuery)
            ->where('total', '>', $myTotal)
            ->count() + 1;

        $level = UserPoint::levelForPoints($myTotal);

        return response()->json([
            'success' => true,
            'data' => [
                'rank'         => $rank,
                'total_points' => $myTotal,
                'level'        => $level['name'],
                'badge'        => $level['badge'],
                'period'       => $period,
            ],
        ]);
    }
}
