<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StocksResource\Pages;
use App\Filament\Resources\StocksResource\RelationManagers;
use App\Models\Stocks;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StocksResource extends Resource
{
    protected static ?string $model = Stocks::class;


    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $label = 'Riwayat Stok';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('products', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) => $record->name
                    )
                    ->reactive()
                    ->required(),
                Select::make('status')
                    ->options([
                        'Masuk' => 'Masuk',
                        'Keluar' => 'Keluar',
                    ])
                    ->reactive()
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->rule(function ($get) {
                        return new class($get) implements Rule {
                            private $get;

                            public function __construct($get)
                            {
                                $this->get = $get;
                            }

                            public function passes($attribute, $value)
                            {
                                $get = $this->get;

                                $status = $get('status');
                                if ($status !== 'Keluar') {
                                    return true;
                                }

                                $productId = $get('product_id');
                                $color = strtolower(trim($get('color') ?? ''));
                                $size  = strtoupper(trim($get('size') ?? ''));

                                if (!$productId || !$color || !$size) {
                                    return true; // Biar 'required' handle ini
                                }

                                // Ambil stok masuk
                                $masuk = \App\Models\Stocks::where('product_id', $productId)
                                    ->whereRaw('LOWER(color) = ?', [$color])
                                    ->whereRaw('UPPER(size) = ?', [$size])
                                    ->where('status', 'Masuk')
                                    ->sum('quantity');

                                // Ambil stok keluar
                                $keluar = \App\Models\Stocks::where('product_id', $productId)
                                    ->whereRaw('LOWER(color) = ?', [$color])
                                    ->whereRaw('UPPER(size) = ?', [$size])
                                    ->where('status', 'Keluar')
                                    ->sum('quantity');

                                $stokTersedia = $masuk - $keluar;

                                if ($stokTersedia <= 0) {
                                    return false;
                                }

                                if ($value > $stokTersedia) {
                                    return false;
                                }

                                return true;
                            }

                            public function message()
                            {
                                $get = $this->get;

                                $productId = $get('product_id');
                                $color = strtolower(trim($get('color') ?? ''));
                                $size  = strtoupper(trim($get('size') ?? ''));

                                $masuk = \App\Models\Stocks::where('product_id', $productId)
                                    ->whereRaw('LOWER(color) = ?', [$color])
                                    ->whereRaw('UPPER(size) = ?', [$size])
                                    ->where('status', 'Masuk')
                                    ->sum('quantity');

                                $keluar = \App\Models\Stocks::where('product_id', $productId)
                                    ->whereRaw('LOWER(color) = ?', [$color])
                                    ->whereRaw('UPPER(size) = ?', [$size])
                                    ->where('status', 'Keluar')
                                    ->sum('quantity');

                                $stokTersedia = $masuk - $keluar;

                                if ($stokTersedia <= 0) {
                                    return '❌ Stok untuk Warna dan Size ini sudah habis (0).';
                                }

                                return '❌ Jumlah keluar tidak boleh melebihi stok saat ini (' . $stokTersedia . ').';
                            }
                        };
                    }),
                TextInput::make('color')
                    ->required(),
                TextInput::make('size')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('products.name')
                    ->label('Nama Produk')
                    ->searchable('products.name'),
                TextColumn::make('color'),
                textcolumn::make('size'),
                TextColumn::make('quantity'),
                BadgeColumn::make('status')
                    ->colors([
                        'info' => 'Masuk',
                        'danger' => 'Keluar'
                    ]),

            ])
            ->filters([
                SelectFilter::make('color')
                    ->label('Warna')
                    ->options(
                        fn() => \App\Models\Stocks::query()
                            ->distinct()
                            ->pluck('color', 'color')
                            ->toArray()
                    ),

                SelectFilter::make('size')
                    ->label('Ukuran')
                    ->options(
                        fn() => \App\Models\Stocks::query()
                            ->distinct()
                            ->pluck('size', 'size')
                            ->toArray()
                    ),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStocks::route('/create'),
            'edit' => Pages\EditStocks::route('/{record}/edit'),
        ];
    }
}
