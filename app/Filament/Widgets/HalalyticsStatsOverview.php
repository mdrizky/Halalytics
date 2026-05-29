<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\BloodEvent;
use App\Models\AiLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HalalyticsStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', (string) User::count())
                ->description('Total user terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Produk', (string) ProductModel::count())
                ->description('Produk dalam database')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            Stat::make('Total Scan', (string) ScanModel::count())
                ->description('Aktivitas scan user')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('warning'),
            Stat::make('Event Donor', (string) BloodEvent::count())
                ->description('Total event donor darah')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
            Stat::make('AI Requests', (string) AiLog::count())
                ->description('Total pemrosesan AI')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('info'),
        ];
    }
}
