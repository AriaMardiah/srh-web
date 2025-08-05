<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class PaymentResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
                TextColumn::make('order_id')
                    ->searchable(),
                TextColumn::make('total_pembayaran'),
                TextColumn::make('metode_pembayaran'),
                BadgeColumn::make('status_pembayaran')
                ->colors([
                    'warning' => 'menunggu',
                    'info' => 'diproses',
                    'success' => 'selesai',
                    'danger' => 'gagal',
                    'gray' => 'kadaluarsa',
                ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('status Pembayaran')
                    ->label('Status Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    // Hanya tampilkan tombol ini jika statusnya 'menunggu'
                    ->visible(condition: fn($record) => $record->metode_pembayaran === 'cod' && $record->status_pembayaran === 'menunggu')
                    // Minta konfirmasi dari user
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Produk')
                    ->modalDescription('Apakah anda yakin produk sudah di bayar?')
                    ->modalSubmitActionLabel('Ya')
                    // Logika yang akan dijalankan saat tombol dikonfirmasi
                    ->action(function ($record) {
                        $record->update([
                            'status_pembayaran' => 'selesai'
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    public static function canEdit(Model $record):bool
    {
        return false;
    }
    public static function canCreate():bool
    {
        return false;
    }
}
