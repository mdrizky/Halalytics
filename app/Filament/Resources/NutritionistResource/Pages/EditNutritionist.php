<?php

namespace App\Filament\Resources\NutritionistResource\Pages;

use App\Filament\Resources\NutritionistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNutritionist extends EditRecord
{
    protected static string $resource = NutritionistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
