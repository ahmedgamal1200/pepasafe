<?php

namespace App\Filament\Resources\WalletRechargeRequestResource\Pages;

use App\Filament\Resources\WalletRechargeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWalletRechargeRequest extends EditRecord
{
    protected static string $resource = WalletRechargeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
