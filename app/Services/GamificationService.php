<?php

namespace App\Services;

use App\Models\CommunityPost;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    private array $levels = [
        0    => 'Pemula',
        100  => 'Penjelajah',
        500  => 'Kontributor',
        1500 => 'Ahli Halal',
        5000 => 'Master Halal',
    ];

    /**
     * Tambah poin ke user
     */
    public function addPoints(int $userId, int $points, string $reason, ?string $refType = null, ?int $refId = null): void
    {
        DB::table('community_point_transactions')->insert([
            'user_id'        => $userId,
            'points'         => $points,
            'reason'         => $reason,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $userPoints = DB::table('community_user_points')
            ->where('user_id', $userId)->first();

        if ($userPoints) {
            $newTotal = $userPoints->total_points + $points;
            DB::table('community_user_points')
                ->where('user_id', $userId)
                ->update([
                    'total_points' => $newTotal,
                    'level'        => $this->getLevelName($newTotal),
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('community_user_points')->insert([
                'user_id'      => $userId,
                'total_points' => $points,
                'level'        => $this->getLevelName($points),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->checkBadges($userId);
    }

    public function getLevelName(int $totalPoints): string
    {
        $level = 'Pemula';
        foreach ($this->levels as $threshold => $name) {
            if ($totalPoints >= $threshold) {
                $level = $name;
            }
        }
        return $level;
    }

    public function getUserStats(int $userId): array
    {
        $up = DB::table('community_user_points')
            ->where('user_id', $userId)->first();

        $badges = DB::table('user_badges')
            ->join('badges', 'badges.id', '=', 'user_badges.badge_id')
            ->where('user_badges.user_id', $userId)
            ->select('badges.name', 'badges.description', 'badges.icon_path', 'user_badges.earned_at')
            ->get();

        return [
            'total_points' => $up->total_points ?? 0,
            'level'        => $up->level ?? 'Pemula',
            'badges'       => $badges,
        ];
    }

    public function getLeaderboard(int $limit = 20): array
    {
        return DB::table('community_user_points')
            ->join('users', 'users.id_user', '=', 'community_user_points.user_id')
            ->select('users.username', 'users.full_name', 'users.avatar_url',
                     'community_user_points.total_points', 'community_user_points.level')
            ->orderByDesc('community_user_points.total_points')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function checkBadges(int $userId): void
    {
        $up = DB::table('community_user_points')
            ->where('user_id', $userId)->first();
        if (!$up) return;

        $postCount = CommunityPost::where('user_id', $userId)->count();

        $allBadges = DB::table('badges')->get();

        foreach ($allBadges as $badge) {
            $already = DB::table('user_badges')
                ->where('user_id', $userId)
                ->where('badge_id', $badge->id)
                ->exists();
            if ($already) continue;

            $earned = false;
            if ($badge->condition_type === 'total_points' && $up->total_points >= $badge->condition_value) {
                $earned = true;
            } elseif ($badge->condition_type === 'post_count' && $postCount >= $badge->condition_value) {
                $earned = true;
            }

            if ($earned) {
                DB::table('user_badges')->insert([
                    'user_id'    => $userId,
                    'badge_id'   => $badge->id,
                    'earned_at'  => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
