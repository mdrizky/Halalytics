<?php

namespace App\Console;

use App\Jobs\BpomSyncJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Every 5 minutes — API health monitoring
        $schedule->job(new \App\Jobs\ApiHealthCheckJob())
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // Daily at 00:05 — Update user streaks
        $schedule->job(new \App\Jobs\UpdateUserStreaksJob())
            ->dailyAt('00:05')
            ->withoutOverlapping();

        // Daily at 01:00 — Expire halal certificates
        $schedule->job(new \App\Jobs\ExpireCertificatesJob())
            ->dailyAt('01:00')
            ->withoutOverlapping();

        // Daily at 02:00 — BPOM data sync
        $schedule->job(new BpomSyncJob())
            ->dailyAt('02:00')
            ->onOneServer()
            ->withoutOverlapping();

        // Daily at 03:00 — Clean old cache & logs
        $schedule->job(new \App\Jobs\CleanOldCacheJob())
            ->dailyAt('03:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
