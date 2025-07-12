<?php

namespace App\Filament\Resources\TranslationValueResource\Pages;

use App\Filament\Resources\TranslationValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTranslationValues extends ListRecords
{
    protected static string $resource = TranslationValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
