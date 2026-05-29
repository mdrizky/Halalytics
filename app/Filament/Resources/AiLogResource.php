<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiLogResource\Pages;
use App\Models\AiLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AiLogResource extends Resource
{
    protected static ?string $model = AiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'AI Control Panel';

    protected static ?string $modelLabel = 'AI Log';

    protected static ?string $pluralModelLabel = 'AI Logs & Analytics';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('user_id')
                    ->relationship('user', 'full_name')
                    ->disabled(),
                Forms\Components\TextInput::make('model_name')
                    ->label('AI Model')
                    ->disabled(),
                Forms\Components\TextInput::make('prompt_tokens')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('completion_tokens')
                    ->numeric()
                    ->disabled(),
                Forms\Components\Textarea::make('prompt')
                    ->columnSpanFull()
                    ->disabled(),
                Forms\Components\Textarea::make('response')
                    ->columnSpanFull()
                    ->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_name')
                    ->label('Model')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_tokens')
                    ->label('Tokens')
                    ->sortable(),
                Tables\Columns\TextColumn::make('response_time_ms')
                    ->label('Latency (ms)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('model_name')
                    ->options([
                        'gpt-4o' => 'GPT-4o',
                        'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                        'claude-3-sonnet' => 'Claude 3 Sonnet',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiLogs::route('/'),
        ];
    }
}
