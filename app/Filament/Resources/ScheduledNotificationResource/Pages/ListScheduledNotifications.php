<?php

namespace App\Filament\Resources\ScheduledNotificationResource\Pages;

use App\Filament\Resources\ScheduledNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScheduledNotifications extends ListRecords
{
    protected static string $resource = ScheduledNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
