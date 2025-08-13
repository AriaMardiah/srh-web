<?php

namespace App\Filament\Widgets;

use App\Models\ModelRequest;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BadgeColumn;

class NewModelRequest extends BaseWidget
{
    protected static ?string $heading = 'New Model Request';


    protected function getTableQuery(): Builder
    {
        return ModelRequest::query()
            ->where('status', 'diproses')
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('user.name')
                ->label('Nama Pengguna')
                ->sortable(),


            ImageColumn::make('file')
                ->label('Gambar')
                ->getStateUsing(fn($record) => asset('storage/' . $record->file))
                ->height(60),

            TextColumn::make('title')
                ->label('Judul'),

            TextColumn::make('description')
                ->label('Deskripsi')
                ->limit(40)
                ->wrap(),

            BadgeColumn::make('status')
                ->colors([
                    'info' => 'diproses'
                ]),
        ];
    }
}
    