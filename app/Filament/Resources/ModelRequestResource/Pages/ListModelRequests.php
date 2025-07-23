<?php

namespace App\Filament\Resources\ModelRequestResource\Pages;

use App\Filament\Resources\ModelRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModelRequests extends ListRecords
{
    protected static string $resource = ModelRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
