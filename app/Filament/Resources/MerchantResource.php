<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Halalytics Expansion';

    protected static ?string $navigationLabel = 'Merchant Marketplace';

    protected static ?string $modelLabel = 'Merchant';

    protected static ?string $pluralModelLabel = 'Merchant';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'toko_halal' => 'Toko Halal',
                    'klinik' => 'Klinik',
                    'apotek' => 'Apotek',
                    'rs' => 'Rumah Sakit',
                    'puskesmas' => 'Puskesmas',
                    'restoran_halal' => 'Restoran Halal',
                ])
                ->required(),
            Forms\Components\Textarea::make('address')
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('latitude')
                ->numeric(),
            Forms\Components\TextInput::make('longitude')
                ->numeric(),
            Forms\Components\TextInput::make('phone')
                ->tel(),
            Forms\Components\TextInput::make('website')
                ->url(),
            Forms\Components\TextInput::make('affiliate_link')
                ->url(),
            Forms\Components\TextInput::make('google_place_id'),
            Forms\Components\KeyValue::make('opening_hours')
                ->label('Jam Operasional')
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('image_path')
                ->disk('public')
                ->directory('marketplace/merchants'),
            Forms\Components\Toggle::make('is_verified')
                ->label('Terverifikasi'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('address')
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified'),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Produk'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }
}
