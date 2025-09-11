<?php

namespace App\Filament\Resources\CustomPlanSubscriptionResource\Pages;

use App\Filament\Resources\CustomPlanSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomPlanSubscriptions extends ListRecords
{
    protected static string $resource = CustomPlanSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
