<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NutritionistResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NutritionistResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Expert Management';

    protected static ?string $modelLabel = 'Ahli Gizi';

    protected static ?string $pluralModelLabel = 'Daftar Ahli Gizi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'ahli_gizi');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('full_name')
                    ->label('Nama Lengkap')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Nomor HP'),
                Forms\Components\Select::make('active')
                    ->label('Status Akun')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Suspend / Nonaktif',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('bio')
                    ->label('Biografi & Keahlian')
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\BadgeColumn::make('active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ]),
                Tables\Columns\TextColumn::make('total_scans')
                    ->label('Total Kontribusi')
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListNutritionists::route('/'),
            'create' => Pages\CreateNutritionist::route('/create'),
            'edit' => Pages\EditNutritionist::route('/{record}/edit'),
        ];
    }
}
