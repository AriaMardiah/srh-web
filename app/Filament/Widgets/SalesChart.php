<?php
// app/Filament/Widgets/SalesChart.php

namespace App\Filament\Widgets;

use App\Models\Orders;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan (30 Hari Terakhir)';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $salesData = Orders::query()
            ->whereBetween('created_at', [$startDate, $endDate])
             // DIGANTI: SUM(total_price) menjadi SUM(total)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as aggregate'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('aggregate', 'date')
            ->all();

        $dates = collect(Carbon::parse($startDate)->toPeriod($endDate)->toArray())->map(fn ($date) => $date->format('Y-m-d'));
        $data = $dates->map(fn ($date) => $salesData[$date] ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $dates->map(fn ($date) => Carbon::parse($date)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
