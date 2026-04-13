<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'AI Expansion';

    protected static ?string $navigationLabel = 'Recipe AI';

    protected static ?string $modelLabel = 'Resep';

    protected static ?string $pluralModelLabel = 'Resep';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Pembuat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_halal_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('substitutions_count')
                    ->label('Switch AI')
                    ->counts('substitutions'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label(fn (Recipe $record) => $record->is_halal_verified ? 'Batalkan Verifikasi' : 'Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->action(fn (Recipe $record) => $record->update([
                        'is_halal_verified' => ! $record->is_halal_verified,
                    ])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
        ];
    }
}
