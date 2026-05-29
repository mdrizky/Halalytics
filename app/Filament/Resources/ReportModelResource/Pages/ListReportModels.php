<?php

namespace App\Filament\Resources\ReportModelResource\Pages;

use App\Filament\Resources\ReportModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportModels extends ListRecords
{
    protected static string $resource = ReportModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
