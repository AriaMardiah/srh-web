<?php
// app/Filament/Widgets/SalesStatsOverview.php

namespace App\Filament\Widgets;

use App\Models\Orders;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Periode 30 hari terakhir
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Data periode saat ini
        $currentPeriodData = Orders::whereBetween('created_at', [$startDate, $endDate]);
        $totalRevenue = $currentPeriodData->sum('total'); // DIGANTI: dari 'total_price' menjadi 'total'
        $totalOrders = $currentPeriodData->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Data periode sebelumnya untuk perbandingan
        $previousStartDate = Carbon::now()->subDays(60);
        $previousEndDate = $startDate;
        $previousPeriodData = Orders::whereBetween('created_at', [$previousStartDate, $previousEndDate]);
        $previousTotalRevenue = $previousPeriodData->sum('total'); // DIGANTI: dari 'total_price' menjadi 'total'
        $previousTotalOrders = $previousPeriodData->count();

        // Hitung persentase perubahan
        $revenueChange = $previousTotalRevenue > 0 ? (($totalRevenue - $previousTotalRevenue) / $previousTotalRevenue) * 100 : 100;
        $ordersChange = $previousTotalOrders > 0 ? (($totalOrders - $previousTotalOrders) / $previousTotalOrders) * 100 : 100;

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description(sprintf('%+.2f%% dari periode sebelumnya', $revenueChange))
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Jumlah Pesanan', number_format($totalOrders))
                ->description(sprintf('%+.2f%% dari periode sebelumnya', $ordersChange))
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'danger'),

            Stat::make('Rata-rata Nilai Pesanan', 'Rp ' . number_format($avgOrderValue, 0, ',', '.'))
                ->description('Dalam 30 hari terakhir')
                ->color('success'),
        ];
    }
}
