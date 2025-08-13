<?php

namespace App\Filament\Widgets;

use App\Models\Order_details;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Produk Terlaris (30 Hari Terakhir)';
    protected static ?int $sort = 1;



    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(30);

        $topProducts = Order_details::query()
            ->whereHas('orders', fn($query) => $query->where('created_at', '>=', $startDate))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Terjual',
                    'data' => $topProducts->pluck('total_quantity'),
                    'backgroundColor' => [
                        '#fa053aff',
                        '#25a2f5ff',
                        '#fdd05eff',
                        '#32f319ff',
                        '#9966FF',
                        '#FF9F40',
                        '#C9CBCF',
                        '#023133ff',
                        '#FF6384',
                        '#21a7b1ff'
                    ],
                ],
            ],
            'labels' => $topProducts->pluck('product_name'),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}
