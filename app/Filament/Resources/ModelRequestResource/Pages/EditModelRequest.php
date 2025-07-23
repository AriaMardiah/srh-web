<?php

namespace App\Filament\Resources\ModelRequestResource\Pages;

use App\Filament\Resources\ModelRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModelRequest extends EditRecord
{
    protected static string $resource = ModelRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
