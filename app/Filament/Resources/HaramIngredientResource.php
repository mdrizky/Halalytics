<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HaramIngredientResource\Pages;
use App\Models\HaramIngredient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HaramIngredientResource extends Resource
{
    protected static ?string $model = HaramIngredient::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'AI Expansion';

    protected static ?string $navigationLabel = 'Bahan Haram OCR';

    protected static ?string $modelLabel = 'Bahan Haram';

    protected static ?string $pluralModelLabel = 'Bahan Haram';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Bahan')
                ->required()
                ->maxLength(255),
            Forms\Components\TagsInput::make('aliases')
                ->label('Alias / Sinonim')
                ->placeholder('Tambah alias bahan')
                ->columnSpanFull(),
            Forms\Components\Select::make('category')
                ->label('Kategori')
                ->options([
                    'haram' => 'Haram',
                    'syubhat' => 'Syubhat',
                    'alergen_umum' => 'Alergen Umum',
                ])
                ->required(),
            Forms\Components\Select::make('severity')
                ->label('Severity')
                ->options([
                    1 => 'Info',
                    2 => 'Warning',
                    3 => 'Danger',
                ])
                ->required(),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        3 => 'Danger',
                        2 => 'Warning',
                        default => 'Info',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('aliases')
                    ->label('Alias')
                    ->formatStateUsing(fn (?array $state): string => implode(', ', $state ?? []))
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn (HaramIngredient $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon('heroicon-o-power')
                    ->action(fn (HaramIngredient $record) => $record->update(['is_active' => ! $record->is_active])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHaramIngredients::route('/'),
            'create' => Pages\CreateHaramIngredient::route('/create'),
            'edit' => Pages\EditHaramIngredient::route('/{record}/edit'),
        ];
    }
}
