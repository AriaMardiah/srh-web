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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(30),
                TextInput::make('price'),
                TextInput::make('description'),
                FileUpload::make('images')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->directory('products')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp']),
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
                    ->wrap(),
                TextColumn::make('grouped_stok')
                    ->label('Stok per Varian')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->grouped_stok->map(function ($item) {
                            return "{$item['color']} - {$item['size']}: {$item['stok_akhir']}";
                        })->implode(', ');
                    })
                    ->wrap(),

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
