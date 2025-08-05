<?php
// app/Filament/Widgets/TopSellingProducts.php

namespace App\Filament\Widgets;

// DIGANTI: Menggunakan model OrderDetail

use App\Models\Order_details;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\RowNumberColumn;
use stdClass; // Diperlukan jika Anda di Filament v2

class TopSellingProducts extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Produk Terlaris (30 Hari Terakhir)';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $startDate = Carbon::now()->subDays(30);

        // DIGANTI: Menggunakan model OrderDetail dan tabel order_details
        return Order_details::query()
            ->whereHas('orders', fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.id', // <-- TAMBAHKAN BARIS INI
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name') // Pastikan groupBy tetap ada
            ->orderByDesc('total_quantity')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        // Sesuaikan dengan versi Filament Anda (v2 atau v3)
        // Ini adalah contoh untuk v2, yang paling kompatibel
        return [
            TextColumn::make('no')
                ->label('No.')
                ->getStateUsing(static function (stdClass $rowLoop): string {
                    return (string) $rowLoop->iteration;
                }),
            TextColumn::make('product_name')->label('Nama Produk')->searchable()->sortable(),
            TextColumn::make('total_quantity')->label('Total Terjual')->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
