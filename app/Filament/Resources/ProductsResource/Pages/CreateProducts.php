<?php

namespace App\Filament\Resources\ProductsResource\Pages;

use App\Filament\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;

   protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.stocks.create', [
            'product_id' => $this->record->id,
        ]);
    }
}
