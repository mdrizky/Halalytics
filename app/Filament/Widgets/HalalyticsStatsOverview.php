<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HalalyticsStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total User', (string) User::count()),
            Stat::make('Total Konsultasi', (string) Consultation::count()),
            Stat::make('Total Post', (string) Post::count()),
            Stat::make(
                'Revenue',
                'Rp ' . number_format((int) Consultation::where('payment_status', 'paid')->sum('amount'), 0, ',', '.')
            ),
        ];
    }
}
