<?php

namespace App\Filament\Resources\TermsAndConditionResource\Pages;

use App\Filament\Resources\TermsAndConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTermsAndCondition extends EditRecord
{
    protected static string $resource = TermsAndConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
