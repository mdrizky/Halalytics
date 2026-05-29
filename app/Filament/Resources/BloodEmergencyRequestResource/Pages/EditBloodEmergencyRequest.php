<?php

namespace App\Filament\Resources\BloodEmergencyRequestResource\Pages;

use App\Filament\Resources\BloodEmergencyRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloodEmergencyRequest extends EditRecord
{
    protected static string $resource = BloodEmergencyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
