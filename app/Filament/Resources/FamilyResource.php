<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyResource\Pages;
use App\Models\FamilyProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FamilyResource extends Resource
{
    protected static ?string $model = FamilyProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $modelLabel = 'Profil Keluarga';

    protected static ?string $pluralModelLabel = 'Data Keluarga User';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Pemilik Akun')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Anggota')
                    ->required(),
                Forms\Components\TextInput::make('relationship')
                    ->label('Hubungan'),
                Forms\Components\TextInput::make('age')
                    ->numeric()
                    ->label('Umur'),
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ]),
                Forms\Components\Textarea::make('allergies')
                    ->label('Alergi'),
                Forms\Components\Textarea::make('medical_history')
                    ->label('Riwayat Medis'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pemilik Akun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('relationship')
                    ->label('Hubungan'),
                Tables\Columns\TextColumn::make('age')
                    ->label('Umur'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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
            'index' => Pages\ListFamilies::route('/'),
            'create' => Pages\CreateFamily::route('/create'),
            'edit' => Pages\EditFamily::route('/{record}/edit'),
        ];
    }
}
