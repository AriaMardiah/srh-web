<?php

namespace App\Filament\Resources\ModelRequestResource\Pages;

use App\Filament\Resources\ModelRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewModelRequest extends ViewRecord
{
    protected static string $resource = ModelRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Model Request')
                    ->schema([
                        ImageEntry::make('file')
                            ->label('')
                            ->getStateUsing(fn($record) => asset('storage/' . $record->file))
                            ->extraAttributes([
                                'style' => 'width:320px; display:flex; justify-content:center;'
                            ])
                            ->extraImgAttributes([
                                'style' => '
                                    max-width: 300px;
                                    width: 100%;
                                    height: auto;
                                    border-radius: 12px;
                                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                    cursor: zoom-in;
                                ',
                                'onclick' => "window.open(this.src, '_blank')"
                            ])
                            ->columnSpan(1),

                        Section::make('')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama Pengguna')
                                    ->formatStateUsing(fn($state) => "<strong>{$state}</strong>")
                                    ->html(),

                                TextEntry::make('title')
                                    ->label('Judul')
                                    ->formatStateUsing(fn($state) => "<strong>{$state}</strong>")
                                    ->html(),

                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->formatStateUsing(fn($state) => "<strong>{$state}</strong>")
                                    ->html(),
                                    
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->colors([
                                        'success' => 'diterima',
                                        'danger' => 'ditolak',
                                        'info' => 'diproses',
                                    ]),
                            ])
                            ->extraAttributes([
                                'style' => 'display:flex; flex-direction:column; gap:12px;'
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->extraAttributes([
                        'style' => 'background:white; padding:20px; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.05);'
                    ]),
            ]);
    }
}
