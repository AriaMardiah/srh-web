<?php

namespace App\Filament\Resources;

use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// TAMBAHKAN USE STATEMENT INI
use App\Filament\Resources\LaporanResource\Pages;
use App\Models\Orders;

class LaporanResource extends Resource
{
    protected static ?string $model = Orders::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Penjualan Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])->actions([])->bulkActions([]);
    }

    // GANTI METHOD getPages() DENGAN YANG INI
    public static function getPages(): array
    {
        return [
            // Gunakan 'Pages' alias untuk merujuk ke halaman kita
            'index' => Pages\LaporanPenjualanProduk::route('/'),
        ];
    }
}
