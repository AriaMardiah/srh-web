<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModelRequestResource\Pages;
use App\Filament\Resources\ModelRequestResource\RelationManagers;
use App\Models\ModelRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\select;

class ModelRequestResource extends Resource
{
    protected static ?string $model = ModelRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pengguna'),
                ImageColumn::make('file')
                    ->getStateUsing(fn($record) => asset('storage/' . $record->file))
                    ->label('Gambar'),
                TextColumn::make('title')
                    ->label('Judul'),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'diterima',
                        'danger' => 'ditolak',
                        'info' => 'diproses',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Diterima')
                    ->label('Diterima')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(condition: fn($record) => $record->status === 'diproses')
                    // Minta konfirmasi dari user
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Produk untuk Diterima')
                    ->modalDescription('Apakah anda yakin merima model ini?')
                    ->modalSubmitActionLabel('Ya, Terima')
                    // Logika yang akan dijalankan saat tombol dikonfirmasi
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'diterima'
                        ]);
                    })
                    ->after(function ($record) {
                        return redirect()->to(
                            route('filament.admin.resources.products.create', [
                                'name' => $record->title,
                                'description' => $record->description,
                                'model_id' => $record->id,
                                'username' => $record->user->name,
                            ])
                        );
                    }),
                Action::make('Ditolak')
                    ->label('Ditolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(condition: fn($record) => $record->status === 'diproses')
                    // Minta konfirmasi dari user
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Produk untuk Ditplak')
                    ->modalDescription('Apakah anda yakin Menolak model ini?')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    // Logika yang akan dijalankan saat tombol dikonfirmasi
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'ditolak'
                        ]);
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
            'index' => Pages\ListModelRequests::route('/'),
            'create' => Pages\CreateModelRequest::route('/create'),
            'edit' => Pages\EditModelRequest::route('/{record}/edit'),
            'view' => Pages\ViewModelRequest::route('/{record}'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
