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
            Stat::make('Donasi Terkumpul', 'Rp ' . number_format(\App\Models\Donation::where('status', 'success')->sum('amount'), 0, ',', '.'))
                ->description('Total donasi dari seluruh kampanye')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Sesi Konsultasi', \App\Models\NutritionConsultation::count())
                ->description('Total konsultasi ahli gizi & admin')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
            Stat::make('Request Produk', \App\Models\ProductRequest::where('status', 'pending')->count())
                ->description('Request produk masuk yang belum diproses')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }
}
