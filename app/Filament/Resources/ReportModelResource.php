<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportModelResource\Pages;
use App\Models\ReportModel;
use App\Models\ProductModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ReportModelResource extends Resource
{
    protected static ?string $model = ReportModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Product Management';

    protected static ?string $modelLabel = 'Laporan Produk';

    protected static ?string $pluralModelLabel = 'Laporan Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'username')
                    ->disabled()
                    ->label('Pelapor'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'nama_product')
                    ->disabled()
                    ->label('Produk Terlapor'),
                Forms\Components\TextInput::make('reason')
                    ->disabled()
                    ->label('Alasan Laporan'),
                Forms\Components\Textarea::make('laporan')
                    ->disabled()
                    ->label('Detail Laporan'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui (Valid)',
                        'rejected' => 'Ditolak (Tidak Valid)',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->rows(3),
            ])->columns(2),

            Forms\Components\Card::make()->schema([
                Forms\Components\FileUpload::make('evidence_image')
                    ->label('Bukti Foto')
                    ->image()
                    ->disabled()
                    ->disk('public'),
            ])->columnSpan(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('product.nama_product')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->badge()
                    ->color(fn (string $state) => str_contains(strtolower($state), 'palsu') ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Pelapor'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Waktu Masuk')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_fake')
                    ->label('Tandai Palsu')
                    ->icon('heroicon-o-hand-raised')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ReportModel $record) => $record->status === 'pending')
                    ->action(function (ReportModel $record) {
                        $record->update(['status' => 'approved', 'admin_notes' => 'Terkonfirmasi palsu melalui laporan user.']);
                        $record->product->update(['verification_status' => 'forgery_confirmed']);
                        
                        Notification::make()
                            ->title('Produk ditandai sebagai PALSU')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('mark_as_suspicious')
                    ->label('Tandai Waspada')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (ReportModel $record) => $record->status === 'pending')
                    ->action(function (ReportModel $record) {
                        $record->update(['status' => 'approved', 'admin_notes' => 'Tandai waspada untuk investigasi lebih lanjut.']);
                        $record->product->update(['verification_status' => 'suspicious']);
                        
                        Notification::make()
                            ->title('Produk ditandai sebagai WASPADA')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReportModels::route('/'),
            'create' => Pages\CreateReportModel::route('/create'),
            'edit' => Pages\EditReportModel::route('/{record}/edit'),
        ];
    }
}
