<?php

namespace App\Filament\Resources\BloodEventResource\Pages;

use App\Filament\Resources\BloodEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBloodEvents extends ListRecords
{
    protected static string $resource = BloodEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
