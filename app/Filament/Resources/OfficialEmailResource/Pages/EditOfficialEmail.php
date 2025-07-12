<?php

namespace App\Filament\Resources\OfficialEmailResource\Pages;

use App\Filament\Resources\OfficialEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfficialEmail extends EditRecord
{
    protected static string $resource = OfficialEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
