<?php

namespace App\Filament\Widgets;

use App\Models\Orders;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BadgeColumn;

class NewOrder extends BaseWidget
{
    protected static ?string $heading = 'Order Terbaru Hari Ini';

    protected function isSearchable(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Orders::query()
                    ->whereDate('created_at', today())
                    ->latest()
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('created_at')
                    ->label('Waktu Pesanan')
                    ->dateTime('d M Y H:i'),

                BadgeColumn::make('status')
                    ->colors([
                        'info' => 'selesai'
                    ]),
            ]);
    }
}
