<?php

namespace App\Filament\Resources\OfficialEmailResource\Pages;

use App\Filament\Resources\OfficialEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfficialEmails extends ListRecords
{
    protected static string $resource = OfficialEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
