<?php

namespace App\Filament\Resources\BloodEmergencyRequestResource\Pages;

use App\Filament\Resources\BloodEmergencyRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBloodEmergencyRequests extends ListRecords
{
    protected static string $resource = BloodEmergencyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
