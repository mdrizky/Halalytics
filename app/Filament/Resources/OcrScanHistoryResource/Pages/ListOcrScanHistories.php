<?php

namespace App\Filament\Resources\OcrScanHistoryResource\Pages;

use App\Filament\Resources\OcrScanHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListOcrScanHistories extends ListRecords
{
    protected static string $resource = OcrScanHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
