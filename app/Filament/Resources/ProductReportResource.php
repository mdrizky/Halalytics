<?php

namespace App\Filament\Resources;

use App\Models\ReportModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ProductReportResource\Pages;

class ProductReportResource extends Resource
{
    protected static ?string $model = ReportModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Safety & Reports';

    protected static ?string $navigationLabel = 'Laporan Produk Palsu';

    protected static ?string $modelLabel = 'Laporan Produk';

    protected static ?string $pluralModelLabel = 'Laporan Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Laporan')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'full_name')
                        ->disabled(),
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'nama_product')
                        ->disabled(),
                    Forms\Components\TextInput::make('reason')
                        ->disabled(),
                    Forms\Components\Textarea::make('laporan')
                        ->label('Deskripsi Laporan')
                        ->disabled(),
                    Forms\Components\FileUpload::make('evidence_image')
                        ->label('Foto Bukti')
                        ->image()
                        ->disabled(),
                ])->columns(2),
            Forms\Components\Section::make('Moderasi Admin')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'under_investigation' => 'Under Investigation',
                            'verified_fake' => 'Verified Fake',
                            'dismissed' => 'Dismissed',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Catatan Admin')
                        ->rows(4),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('evidence_image')
                    ->label('Bukti'),
                Tables\Columns\TextColumn::make('product.nama_product')
                    ->label('Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pelapor'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'verified_fake' => 'danger',
                        'under_investigation' => 'warning',
                        'dismissed' => 'gray',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Waktu'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReports::route('/'),
            'edit' => Pages\EditProductReport::route('/{record}/edit'),
        ];
    }
}
