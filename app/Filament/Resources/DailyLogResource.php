<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyLogResource\Pages;
use App\Models\DailyLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DailyLogResource extends Resource
{
    protected static ?string $model = DailyLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'AI Expansion';

    protected static ?string $navigationLabel = 'Log Nutrisi AI';

    protected static ?string $modelLabel = 'Log Nutrisi';

    protected static ?string $pluralModelLabel = 'Log Nutrisi';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meal_type')
                    ->label('Tipe Makan')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_calories')
                    ->label('Kalori')
                    ->suffix(' kkal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_protein')
                    ->label('Protein')
                    ->suffix(' g'),
                Tables\Columns\TextColumn::make('total_carbs')
                    ->label('Karbo')
                    ->suffix(' g'),
                Tables\Columns\TextColumn::make('total_fat')
                    ->label('Lemak')
                    ->suffix(' g'),
                Tables\Columns\TextColumn::make('logged_at')
                    ->label('Tanggal')
                    ->date(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyLogs::route('/'),
        ];
    }
}
