<?php

namespace App\Filament\Resources\ModelRequestResource\Pages;

use App\Filament\Resources\ModelRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewModelRequest extends ViewRecord
{
    protected static string $resource = ModelRequestResource::class;
    protected static string $view = 'filament.resources.model-request-resource.pages.view-model-request';

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
