<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\ProductModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = ProductModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Product Management';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Daftar Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('nama_product')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barcode')
                    ->label('Barcode')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\TextInput::make('brand')
                    ->label('Merek'),
                Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'unverified' => 'Belum Terverifikasi',
                        'verified' => 'Terverifikasi',
                        'suspicious' => 'Mencurigakan',
                        'forgery_confirmed' => 'Palsu Terkonfirmasi',
                    ])
                    ->default('unverified'),
                Forms\Components\Select::make('approval_status')
                    ->label('Status Persetujuan')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending'),
            ])->columns(2),

            Forms\Components\Card::make()->schema([
                Forms\Components\Textarea::make('komposisi')
                    ->label('Komposisi')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->disk('public')
                    ->directory('product_images'),
            ])->columns(1),

            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('sugar_g')
                    ->label('Gula (g)')
                    ->numeric(),
                Forms\Components\TextInput::make('fat_g')
                    ->label('Lemak (g)')
                    ->numeric(),
                Forms\Components\TextInput::make('calories')
                    ->label('Kalori (kkal)')
                    ->numeric(),
                Forms\Components\TextInput::make('protein_g')
                    ->label('Protein (g)')
                    ->numeric(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar'),
                Tables\Columns\TextColumn::make('nama_product')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->label('Verifikasi')
                    ->colors([
                        'warning' => 'unverified',
                        'success' => 'verified',
                        'danger' => 'forgery_confirmed',
                    ]),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label('Persetujuan')
                    ->colors([
                        'secondary' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori'),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'unverified' => 'Belum Terverifikasi',
                        'verified' => 'Terverifikasi',
                        'suspicious' => 'Mencurigakan',
                        'forgery_confirmed' => 'Palsu Terkonfirmasi',
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
