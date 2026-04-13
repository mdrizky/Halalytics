<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OcrScanHistoryResource\Pages;
use App\Models\OcrScanHistory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OcrScanHistoryResource extends Resource
{
    protected static ?string $model = OcrScanHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'AI Expansion';

    protected static ?string $navigationLabel = 'Riwayat OCR';

    protected static ?string $modelLabel = 'Scan OCR';

    protected static ?string $pluralModelLabel = 'Scan OCR';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Produk')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('user.username')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->badge(),
                Tables\Columns\TextColumn::make('detected_haram')
                    ->label('Deteksi')
                    ->formatStateUsing(fn (?array $state): string => implode(', ', $state ?? []))
                    ->limit(50),
                Tables\Columns\TextColumn::make('scanned_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOcrScanHistories::route('/'),
        ];
    }
}
