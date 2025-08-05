<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Filament\Resources\OrdersResource\RelationManagers;
use App\Models\Orders;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Id Pesanan'),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total Price'),
                BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'Belum Bayar',
                        'warning' => 'Dikemas',
                        'info' => 'Dikirim',
                        'success' => 'Selesai',
                    ])
                    ->label('Status Pesanan'),
                TextColumn::make('order_details.products.name')
                    ->label('Products Name'),
                TextColumn::make('order_details.quantity')
                    ->label('Quantity'),
                BadgeColumn::make('payments.status_pembayaran')
                    ->colors([
                    'warning' => 'menunggu',
                    'info' => 'diproses',
                    'success' => 'selesai',
                    'danger' => 'gagal',
                    'gray' => 'kadaluarsa',
                ])
                    ->label('Status Pembayaran'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Belum Bayar' => 'Belum Bayar',
                        'Dikemas'     => 'Dikemas',
                        'Dikirim'     => 'Dikirim',
                        'Selesai'     => 'Selesai',
                    ])
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('status Barang')
                    ->label('Kirim Pesanan')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    // Hanya tampilkan tombol ini jika statusnya 'menunggu'
                    ->visible(condition: fn($record) => $record->status === 'Dikemas')
                    // Minta konfirmasi dari user
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Produk untuk Dikirim')
                    ->modalDescription('Apakah anda yakin produk menjadi status Dikirim?')
                    ->modalSubmitActionLabel('Ya, Dikirim')
                    // Logika yang akan dijalankan saat tombol dikonfirmasi
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Dikirim'
                        ]);
                        // Kirim notifikasi sukses
                        // Notification::make()
                        //     ->title('Pembayaran Diterima')
                        //     ->body('Status pembayaran telah berhasil diubah menjadi "Selesai".')
                        //     ->success()
                        //     ->send();
                    }),
                
                Action::make('status pesanan selesai')
                    ->label('status pesanan selesai')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    // Hanya tampilkan tombol ini jika statusnya 'menunggu'
                    ->visible(condition: fn($record) => $record->status === 'Dikirim')
                    // Minta konfirmasi dari user
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Produk sudah di antar')
                    ->modalDescription('Apakah anda yakin produk sudah di antar?')
                    ->modalSubmitActionLabel('Ya, sudah')
                    // Logika yang akan dijalankan saat tombol dikonfirmasi
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Selesai'
                        ]);




                        // Kirim notifikasi sukses
                        // Notification::make()
                        //     ->title('Pembayaran Diterima')
                        //     ->body('Status pembayaran telah berhasil diubah menjadi "Selesai".')
                        //     ->success()
                        //     ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
}
