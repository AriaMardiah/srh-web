<?php

namespace App\Filament\Widgets;

use App\Models\Orders;
use App\Models\Products;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Hari Ini & Kemarin
        $todaysRevenue = Orders::whereDate('created_at', $today)->sum('total');
        $yesterdaysRevenue = Orders::whereDate('created_at', $yesterday)->sum('total');
        $revenueChange = $this->calculatePercentageChange($yesterdaysRevenue, $todaysRevenue);

        // Bulan Ini & Bulan Lalu
        $startOfThisMonth = Carbon::now()->startOfMonth();
        $endOfThisMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $monthlyOrders = Orders::whereBetween('created_at', [$startOfThisMonth, $endOfThisMonth])->count();
        $lastMonthOrders = Orders::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderChange = $this->calculatePercentageChange($lastMonthOrders, $monthlyOrders);

        // Produk Habis Stok Sekarang vs Bulan Lalu
        $productWithNoStock = Products::all()->filter(fn($product) => $product->grouped_stok->sum('stock') <= 0)->count();

        // (Opsional) Anggap kita tidak menyimpan histori stok, jadi perbandingan stok tidak tersedia

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaysRevenue, 0, ',', '.'))
                ->description("Perbandingan Dari Hari Kemarin: {$revenueChange['label']}")
                ->color($revenueChange['color']),

            Stat::make('Pesanan Bulan Ini', number_format($monthlyOrders))
                ->description("Perbandingan dari bulan lalu: {$orderChange['label']}")
                ->color($orderChange['color']),

            Stat::make('Produk Habis Stok', $productWithNoStock)
                ->description('Jumlah produk yang stoknya habis')
                ->color('danger'),
        ];
    }

    private function calculatePercentageChange($previous, $current): array
    {
        if ($previous == 0 && $current == 0) {
            return ['label' => 'Tidak berubah', 'color' => 'gray'];
        }

        if ($previous == 0) {
            return ['label' => '+100%', 'color' => 'success'];
        }

        $change = (($current - $previous) / abs($previous)) * 100;
        $formattedChange = number_format($change, 1);

        return [
            'label' => ($change >= 0 ? '+' : '') . $formattedChange . '%',
            'color' => $change >= 0 ? 'success' : 'danger',
        ];
    }
}
