<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Halalytics Expansion';

    protected static ?string $navigationLabel = 'Moderasi Komunitas';

    protected static ?string $modelLabel = 'Postingan';

    protected static ?string $pluralModelLabel = 'Postingan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('title')
                ->maxLength(255),
            Forms\Components\Select::make('category')
                ->options([
                    'resep' => 'Resep',
                    'diskusi' => 'Diskusi',
                    'tips' => 'Tips',
                    'progress' => 'Progress',
                    'tanya' => 'Tanya',
                ])
                ->required(),
            Forms\Components\Textarea::make('content')
                ->required()
                ->rows(6)
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('image_path')
                ->label('Gambar')
                ->disk('public')
                ->directory('community/posts'),
            Forms\Components\Toggle::make('is_pinned')
                ->label('Sematkan'),
            Forms\Components\Toggle::make('is_hidden')
                ->label('Sembunyikan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('content')
                    ->label('Konten')
                    ->limit(60),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes'),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Komentar'),
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Pinned')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_hidden')
                    ->label('Hidden')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('hide')
                    ->label(fn (Post $record) => $record->is_hidden ? 'Tampilkan' : 'Hide')
                    ->icon('heroicon-o-eye-slash')
                    ->action(fn (Post $record) => $record->update(['is_hidden' => ! $record->is_hidden])),
                Tables\Actions\Action::make('pin')
                    ->label(fn (Post $record) => $record->is_pinned ? 'Unpin' : 'Pin')
                    ->icon('heroicon-o-bookmark')
                    ->action(fn (Post $record) => $record->update(['is_pinned' => ! $record->is_pinned])),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
