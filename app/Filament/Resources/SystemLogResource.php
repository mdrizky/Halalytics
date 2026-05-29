<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Report System';

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Logs & Audit';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SCAN_FOOD' => 'success',
                        'SCAN_EMERGENCY_QR' => 'warning',
                        'ADD_MEDICINE' => 'info',
                        'LOGIN' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_risk_detected')
                    ->label('Risiko')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'SCAN_FOOD' => 'Scan Makanan',
                        'SCAN_EMERGENCY_QR' => 'Scan QR Darurat',
                        'ADD_MEDICINE' => 'Tambah Obat',
                        'LOGIN' => 'Login',
                    ]),
                Tables\Filters\TernaryFilter::make('is_risk_detected')
                    ->label('Terdeteksi Risiko'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemLogs::route('/'),
        ];
    }
}
