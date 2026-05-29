<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\User;
use App\Services\AdminBroadcastNotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class NotificationResource extends Resource
{
    protected static ?string $model = User::class; // We use User model to select recipients

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Notification Management';

    protected static ?string $modelLabel = 'Push Notification';

    protected static ?string $pluralModelLabel = 'Broadcast Notifikasi';

    protected static ?string $slug = 'broadcast-notifications';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Notifikasi')
                    ->required()
                    ->placeholder('Contoh: Update Aplikasi Terbaru'),
                Forms\Components\Textarea::make('body')
                    ->label('Isi Pesan')
                    ->required()
                    ->rows(3),
                Forms\Components\Select::make('type')
                    ->label('Tipe Notifikasi')
                    ->options([
                        'info' => 'Informasi',
                        'emergency' => 'Darurat',
                        'event' => 'Event / Donor',
                        'promo' => 'Promo / Iklan',
                    ])
                    ->default('info'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nama'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('role')->badge(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('broadcast')
                    ->label('Kirim Broadcast')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('body')->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'all' => 'Semua',
                                'user' => 'User',
                                'ahli_gizi' => 'Ahli Gizi',
                            ])->default('all'),
                    ])
                    ->action(function (array $data) {
                        $service = app(AdminBroadcastNotificationService::class);
                        
                        if ($data['role'] === 'all') {
                            $result = $service->broadcast($data['title'], $data['body']);
                        } else {
                            $userIds = User::where('role', $data['role'])->pluck('id_user')->toArray();
                            $result = $service->broadcastToUsers($userIds, $data['title'], $data['body']);
                        }
                        
                        Notification::make()
                            ->title($result['success'] ? "Notifikasi sedang diproses" : "Gagal mengirim")
                            ->status($result['success'] ? 'success' : 'danger')
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
        ];
    }
}
