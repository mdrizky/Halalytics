<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BloodEmergencyRequestResource\Pages;
use App\Filament\Resources\BloodEmergencyRequestResource\RelationManagers;
use App\Models\BloodEmergencyRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BloodEmergencyRequestResource extends Resource
{
    protected static ?string $model = BloodEmergencyRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hospital_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('blood_type_needed')
                    ->required(),
                Forms\Components\TextInput::make('bags_needed')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('urgency_level')
                    ->required(),
                Forms\Components\TextInput::make('contact_person')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_phone')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_fulfilled')
                    ->required(),
                Forms\Components\DateTimePicker::make('fulfilled_at'),
                Forms\Components\TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hospital_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('blood_type_needed'),
                Tables\Columns\TextColumn::make('bags_needed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('urgency_level'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_fulfilled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fulfilled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBloodEmergencyRequests::route('/'),
            'create' => Pages\CreateBloodEmergencyRequest::route('/create'),
            'edit' => Pages\EditBloodEmergencyRequest::route('/{record}/edit'),
        ];
    }
}
