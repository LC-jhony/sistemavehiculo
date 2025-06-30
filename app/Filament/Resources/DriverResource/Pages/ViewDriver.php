<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class ViewDriver extends ViewRecord
{
    protected static string $resource = DriverResource::class;

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
                Infolists\Components\Section::make('Datos del Chofer')
                    ->description('Datos generales del chofer y documnetos')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Infolists\Components\Grid::make()
                            ->columns(4)
                            ->schema([
                                Infolists\Components\Card::make('Datos Personales')
                                    ->columnSpan(1)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->label('Nombre'),
                                        Infolists\Components\TextEntry::make('last_paternal_name')
                                            ->label('Apellido Paterno'),
                                        Infolists\Components\TextEntry::make('last_maternal_name')
                                            ->label('Apellido Materno'),
                                        Infolists\Components\TextEntry::make('dni')
                                            ->label('DNI'),
                                        Infolists\Components\TextEntry::make('cargo.name')
                                            ->label('Cargo')
                                            ->badge(),
                                    ]),
                                Infolists\Components\Grid::make()
                                    ->columnSpan(3)
                                    ->schema([
                                        PdfViewerEntry::make('file')
                                            ->label('Vista de PDF')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
