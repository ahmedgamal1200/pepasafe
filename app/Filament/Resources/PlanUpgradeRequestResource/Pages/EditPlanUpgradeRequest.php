<?php

namespace App\Filament\Resources\PlanUpgradeRequestResource\Pages;

use App\Filament\Resources\PlanUpgradeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanUpgradeRequest extends EditRecord
{
    protected static string $resource = PlanUpgradeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
