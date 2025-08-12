<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Orders;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan 30 Hari Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect();
        $sales = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates->push($date->format('d M'));

            $dailySales = Orders::whereDate('created_at', $date)->sum('total'); // atau sesuaikan kolomnya
            $sales->push($dailySales);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $sales,
                    'fill' => true,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    
}
