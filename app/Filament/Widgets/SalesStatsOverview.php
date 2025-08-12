<?php

namespace App\Filament\Widgets;

use App\Models\Orders;
use App\Models\Products; // Pastikan ini adalah model produk
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $today = Carbon::today();
        $todaysRevenue = Orders::whereDate('created_at', $today)->sum('total');

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyOrders = Orders::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        $productWithNoStock = Products::all()->filter(function ($product) {
            return $product->grouped_stok->sum('stock') <= 0;
        })->count();


        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaysRevenue, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->color('info'),

            Stat::make('Pesanan Bulan Ini', number_format($monthlyOrders))
                ->description('Total pesanan bulan ini')
                ->color('info'),

            Stat::make('Produk Habis Stok', $productWithNoStock)
                ->description('Jumlah produk tanpa stok')
                ->color('danger'),
        ];
    }
}
