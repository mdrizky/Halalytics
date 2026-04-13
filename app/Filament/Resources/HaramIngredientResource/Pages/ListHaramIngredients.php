<?php

namespace App\Filament\Resources\HaramIngredientResource\Pages;

use App\Filament\Resources\HaramIngredientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHaramIngredients extends ListRecords
{
    protected static string $resource = HaramIngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
