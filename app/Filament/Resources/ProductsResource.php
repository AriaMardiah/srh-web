<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Filament\Resources\ProductsResource\RelationManagers;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\text;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('model_id')
                    ->default(fn() => request()->query('model_id')),
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(30)
                    ->default(fn() => request()->query('name')),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->maxLength(6),
                TextInput::make('description')
                    ->default(fn() => request()->query('description'))
                    ->required(),
                FileUpload::make('images')
                    ->image()
                    ->required()
                    ->imagePreviewHeight('150')
                    ->directory('products')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->default(fn() => $data['images'] ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->getStateUsing(fn($record) => asset('storage/' . $record->images)),
                TextColumn::make('name'),
                TextColumn::make('price')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('grouped_stok')
                    ->label('Stok per Varian')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->grouped_stok->map(function ($item) {
                            return "{$item['color']} - {$item['size']}: {$item['stock']}";
                        })->implode(', ');
                    })
                    ->wrap(),
                TextColumn::make('model.user.name')
                    ->label('Di request oleh')
                    ->default(fn($record) => $record->model->user->name ?? 'Admin'),

                TextColumn::make('created_at'),
                TextColumn::make('updated_at'),

            ])
            ->filters([
                //
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
