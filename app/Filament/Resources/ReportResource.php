<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\PostReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = PostReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Halalytics Expansion';

    protected static ?string $navigationLabel = 'Laporan Komunitas';

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'reviewed' => 'Reviewed',
                    'resolved' => 'Resolved',
                ])
                ->required(),
            Forms\Components\Textarea::make('description')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.id')
                    ->label('Post ID'),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pelapor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'resolved' => 'success',
                        'reviewed' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Masuk'),
            ])
            ->actions([
                Tables\Actions\Action::make('reviewed')
                    ->label('Reviewed')
                    ->color('warning')
                    ->action(fn (PostReport $record) => $record->update(['status' => 'reviewed'])),
                Tables\Actions\Action::make('resolved')
                    ->label('Resolved')
                    ->color('success')
                    ->action(fn (PostReport $record) => $record->update(['status' => 'resolved'])),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
