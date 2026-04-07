<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpertResource\Pages;
use App\Models\Expert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpertResource extends Resource
{
    protected static ?string $model = Expert::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Halalytics Expansion';

    protected static ?string $navigationLabel = 'Verifikasi Pakar';

    protected static ?string $modelLabel = 'Pakar';

    protected static ?string $pluralModelLabel = 'Pakar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('specialization')
                ->label('Spesialisasi')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('bio')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('certificate_path')
                ->label('Sertifikat')
                ->disk('public')
                ->directory('expert-certificates'),
            Forms\Components\Toggle::make('is_verified')
                ->label('Terverifikasi'),
            Forms\Components\Toggle::make('is_online')
                ->label('Online'),
            Forms\Components\TextInput::make('price_per_session')
                ->label('Tarif per Sesi')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialization')
                    ->label('Spesialisasi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_online')
                    ->label('Online')
                    ->boolean(),
                Tables\Columns\TextColumn::make('price_per_session')
                    ->label('Tarif')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->label('Diupdate'),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (Expert $record) => ! $record->is_verified)
                    ->action(fn (Expert $record) => $record->update(['is_verified' => true])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Expert $record) => $record->is_verified)
                    ->action(function (Expert $record) {
                        $record->update([
                            'is_verified' => false,
                            'is_online' => false,
                        ]);
                    }),
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
            'index' => Pages\ListExperts::route('/'),
            'create' => Pages\CreateExpert::route('/create'),
            'edit' => Pages\EditExpert::route('/{record}/edit'),
        ];
    }
}
