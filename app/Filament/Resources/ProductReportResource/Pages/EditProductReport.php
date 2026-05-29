<?php

namespace App\Filament\Resources\ProductReportResource\Pages;

use App\Filament\Resources\ProductReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductReport extends EditRecord
{
    protected static string $resource = ProductReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
