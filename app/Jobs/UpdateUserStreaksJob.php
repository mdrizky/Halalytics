<?php

namespace App\Jobs;

use App\Models\UserPoint;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserStreaksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 300;

    public function handle(): void
    {
        $yesterday = now()->subDay()->toDateString();

        // Get users who had activity yesterday
        $activeUserIds = DB::table('scan_history')
            ->whereDate('created_at', $yesterday)
            ->distinct()
            ->pluck('user_id');

        // Get users who DID NOT have activity yesterday — reset streak
        User::whereNotIn('id', $activeUserIds)
            ->where('scan_streak', '>', 0)
            ->update(['scan_streak' => 0]);

        // Users who were active: increment streak
        User::whereIn('id', $activeUserIds)
            ->increment('scan_streak');

        // Award streak milestones
        $milestones = [
            7 => ['points' => 100, 'desc' => 'Streak 7 hari berturut-turut! 🔥'],
            14 => ['points' => 200, 'desc' => 'Streak 14 hari! Konsisten! 💪'],
            30 => ['points' => 500, 'desc' => 'Streak 30 hari! Luar biasa! 🏆'],
            60 => ['points' => 1000, 'desc' => 'Streak 60 hari! Master Scanner! 👑'],
            100 => ['points' => 2000, 'desc' => 'Streak 100 hari! LEGENDA! 🌟'],
        ];

        foreach ($milestones as $days => $reward) {
            $users = User::whereIn('id', $activeUserIds)
                ->where('scan_streak', $days)
                ->get();

            foreach ($users as $user) {
                UserPoint::award(
                    $user->id,
                    $reward['points'],
                    'streak',
                    $reward['desc']
                );
            }
        }

        Log::info("Streak update complete. Active users yesterday: {$activeUserIds->count()}");
    }
}
