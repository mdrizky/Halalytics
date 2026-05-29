<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Artikel Edukasi';

    protected static ?string $pluralModelLabel = 'Daftar Artikel';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Artikel')
                    ->required()
                    ->lazy()
                    ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'halal' => 'Halal Education',
                        'health' => 'Health & Nutrition',
                        'donor' => 'Donor Info',
                        'news' => 'Berita',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('author')
                    ->label('Penulis')
                    ->default('Admin Halalytics'),
                Forms\Components\FileUpload::make('image')
                    ->label('Banner Artikel')
                    ->image()
                    ->directory('article_banners'),
                Forms\Components\Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->label('Konten Artikel')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->label('Publikasikan')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'halal',
                        'success' => 'health',
                        'warning' => 'donor',
                    ]),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'halal' => 'Halal Education',
                        'health' => 'Health & Nutrition',
                        'donor' => 'Donor Info',
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
        ];
    }
}
