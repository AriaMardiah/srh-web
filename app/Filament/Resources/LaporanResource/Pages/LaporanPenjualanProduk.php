<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use App\Models\Order_details;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanProduk extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\LaporanResource::class;
    protected static string $view = 'filament.resources.laporan-resource.pages.laporan-penjualan-produk';
    protected static ?string $title = 'Laporan Penjualan per Produk';

    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;
    public Collection $reportData;
    public float $grandTotal = 0;

    public array $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->reportData = collect();
    }

    private function getReportQuery(): Builder
    {
        return Order_details::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereYear('orders.created_at', $this->selectedYear)
            ->whereMonth('orders.created_at', $this->selectedMonth)
            ->where('orders.status', 'Selesai')
            ->selectRaw('
                products.name as product_name,
                SUM(order_details.quantity) as total_quantity,
                SUM(order_details.quantity * products.price) as total_sales
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales');
    }

    public function generateReport(): void
    {
        if (!$this->selectedMonth || !$this->selectedYear) {
            Notification::make()->title('Input tidak lengkap')->body('Silakan pilih Bulan dan Tahun.')->danger()->send();
            return;
        }

        $this->reportData = $this->getReportQuery()->get();
        $this->grandTotal = $this->reportData->sum('total_sales');

        if ($this->reportData->isEmpty()) {
            Notification::make()->title('Laporan Kosong')->body('Tidak ada data penjualan ditemukan.')->warning()->send();
        } else {
            Notification::make()->title('Laporan berhasil dibuat')->body('Tabel laporan telah diperbarui.')->success()->send();
        }
    }

    /**
     * DIPERBARUI: Menjalankan query-nya sendiri, tidak bergantung pada state.
     */
    public function exportPdf()
    {
        // 1. Jalankan query untuk mendapatkan data yang fresh.
        $dataForPdf = $this->getReportQuery()->get();

        // 2. Cek apakah data yang baru diambil ini kosong.
        if ($dataForPdf->isEmpty()) {
            Notification::make()
                ->title('Gagal mengunduh PDF')
                ->body('Tidak ada data untuk diekspor ke PDF.')
                ->warning()
                ->send();
            return;
        }

        // 3. Hitung totalnya
        $totalForPdf = $dataForPdf->sum('total_sales');

        // 4. Buat PDF dengan data yang baru diambil.
        $pdf = Pdf::loadView('pdf.laporan-penjualan-produk', [
            'reportData' => $dataForPdf,
            'grandTotal' => $totalForPdf,
            'bulan' => $this->months[$this->selectedMonth],
            'tahun' => $this->selectedYear,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-penjualan-' . $this->months[$this->selectedMonth] . '-' . $this->selectedYear . '.pdf');
    }
}
