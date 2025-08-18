<?php

namespace App\Filament\Resources\ScheduledNotificationResource\Pages;

use App\Filament\Resources\ScheduledNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduledNotification extends EditRecord
{
    protected static string $resource = ScheduledNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
