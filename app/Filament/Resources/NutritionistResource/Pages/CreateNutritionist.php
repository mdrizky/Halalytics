<?php

namespace App\Filament\Resources\NutritionistResource\Pages;

use App\Filament\Resources\NutritionistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNutritionist extends CreateRecord
{
    protected static string $resource = NutritionistResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'ahli_gizi';
        return $data;
    }
}
