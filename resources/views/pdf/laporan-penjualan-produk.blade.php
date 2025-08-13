<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Produk</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; font-weight: bold; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row th, .total-row td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        Total penjualan dibulan {{ $bulan }} {{ $tahun }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th>nama produk</th>
                <th style="width:20%;">jumlah terjual</th>
                <th style="width:30%;">total Harga jual produk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->total_quantity }} pcs</td>
                    <td>Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data penjualan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" style="text-align: left;">Total Penjualan</th>
                <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
