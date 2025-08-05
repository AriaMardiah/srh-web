<x-filament-panels::page>
    {{-- Bagian Filter --}}
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            {{-- Dropdown Bulan --}}
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Bulan</label>
                <select id="month" wire:model="selectedMonth" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Input Tahun --}}
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tahun</label>
                <input type="number" id="year" wire:model="selectedYear" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 2025">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-end space-x-3">
                <x-filament::button wire:click="generateReport">
                    Buat Laporan
                </x-filament::button>
                <x-filament::button wire:click="exportPdf" color="success" icon="heroicon-o-document-arrow-down" :disabled="$reportData->isEmpty()">
                    Download PDF
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- Bagian Tabel Hasil --}}
    <div class="mt-6 overflow-x-auto bg-white rounded-xl shadow-md dark:bg-gray-800">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama Produk</th>
                    <th class="px-6 py-3">Jumlah Terjual</th>
                    <th class="px-6 py-3">Total Harga Jual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item->product_name }}</td>
                        <td class="px-6 py-4">{{ $item->total_quantity }} pcs</td>
                        <td class="px-6 py-4">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">Silakan buat laporan untuk menampilkan data.</td>
                    </tr>
                @endforelse
            </tbody>
             @if($reportData->isNotEmpty())
            <tfoot>
                <tr class="font-semibold text-gray-900 bg-gray-50 dark:text-white dark:bg-gray-700">
                    <th colspan="3" class="px-6 py-3 text-base text-left">Total Penjualan</th>
                    <td class="px-6 py-3 text-base">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-filament-panels::page>
