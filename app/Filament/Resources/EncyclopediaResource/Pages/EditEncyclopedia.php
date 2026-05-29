<?php

namespace App\Filament\Resources\EncyclopediaResource\Pages;

use App\Filament\Resources\EncyclopediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEncyclopedia extends EditRecord
{
    protected static string $resource = EncyclopediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
