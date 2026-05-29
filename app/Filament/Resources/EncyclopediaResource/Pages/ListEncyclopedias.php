<?php

namespace App\Filament\Resources\EncyclopediaResource\Pages;

use App\Filament\Resources\EncyclopediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEncyclopedias extends ListRecords
{
    protected static string $resource = EncyclopediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
