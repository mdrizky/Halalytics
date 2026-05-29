<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EncyclopediaResource\Pages;
use App\Models\HealthEncyclopedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EncyclopediaResource extends Resource
{
    protected static ?string $model = HealthEncyclopedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Ensiklopedia';

    protected static ?string $pluralModelLabel = 'Daftar Ensiklopedia';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'halal' => 'Halal',
                        'health' => 'Kesehatan',
                        'ingredient' => 'Bahan Makanan',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Gambar Utama')
                    ->directory('encyclopedia'),
                Forms\Components\RichEditor::make('content')
                    ->label('Konten Lengkap')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('keywords')
                    ->label('Kata Kunci (untuk Search)'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Gambar'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'halal',
                        'success' => 'health',
                        'warning' => 'ingredient',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'halal' => 'Halal',
                        'health' => 'Kesehatan',
                        'ingredient' => 'Bahan Makanan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEncyclopedias::route('/'),
            'create' => Pages\CreateEncyclopedia::route('/create'),
            'edit' => Pages\EditEncyclopedia::route('/{record}/edit'),
        ];
    }
}
