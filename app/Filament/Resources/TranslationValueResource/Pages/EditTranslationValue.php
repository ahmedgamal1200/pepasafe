<?php

namespace App\Filament\Resources\TranslationValueResource\Pages;

use App\Filament\Resources\TranslationValueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTranslationValue extends EditRecord
{
    protected static string $resource = TranslationValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
