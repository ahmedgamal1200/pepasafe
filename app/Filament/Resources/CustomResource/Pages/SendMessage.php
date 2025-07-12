<?php

namespace App\Filament\Resources\CustomResource\Pages;

use App\Filament\Resources\CustomResource;
use Filament\Resources\Pages\Page;

class SendMessage extends Page
{
    protected static string $resource = CustomResource::class;

    protected static string $view = 'filament.resources.custom-resource.pages.send-message';
}
