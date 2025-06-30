<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;


class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Datos del Vehiculo')
                    ->icon('heroicon-o-tag')
                    ->columns(5)
                    ->schema([
                        Infolists\Components\TextEntry::make('placa')
                            ->label('Placa'),
                        Infolists\Components\TextEntry::make('modelo')
                            ->label('Modelo'),
                        Infolists\Components\TextEntry::make('marca')
                            ->label('Marca'),
                        Infolists\Components\TextEntry::make('year')
                            ->label('Año')
                            ->badge(),
                        Infolists\Components\IconEntry::make('status')
                            ->label('Estado')
                            ->boolean(),

                    ]),
                Infolists\Components\Section::make('Documentos del Vehiculo')
                    ->icon('bi-file-pdf-fill')
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->label('Tipo de Documento'),
                                PdfViewerEntry::make('file')
                                    ->label('Archivo')
                                    ->label('View the PDF')
                                    ->minHeight('40svh')
                                    ->fileUrl(function ($state, $record) {
                                        return Storage::url($state);
                                    })
                            ])

                    ])
            ]);
    }
}
