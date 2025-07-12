<?php

namespace App\Filament\Resources\WalletRechargeRequestResource\Pages;

use App\Filament\Resources\WalletRechargeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWalletRechargeRequests extends ListRecords
{
    protected static string $resource = WalletRechargeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
