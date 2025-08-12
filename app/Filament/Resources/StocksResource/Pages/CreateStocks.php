<?php

namespace App\Filament\Resources\StocksResource\Pages;

use App\Filament\Resources\StocksResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


class CreateStocks extends CreateRecord
{
    protected static string $resource = StocksResource::class;
    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.products.index');
    }
}
