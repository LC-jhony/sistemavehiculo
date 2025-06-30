<?php

namespace App\Filament\Resources\DriverLicenseResource\Pages;

use App\Filament\Resources\DriverLicenseResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;

use Filament\Resources\Pages\ViewRecord;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;


class ViewDriverLicense extends ViewRecord
{
    protected static string $resource = DriverLicenseResource::class;

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
                                        Infolists\Components\TextEntry::make('driver.full_name')
                                            ->label('Nombre')
                                            ->getStateUsing(fn($record) => $record->driver->name . ' ' . $record->driver->last_paternal_name . ' ' . $record->driver->last_maternal_name),
                                        Infolists\Components\TextEntry::make('license_number')
                                            ->label('Número de Licencia'),
                                        Infolists\Components\TextEntry::make('expiration_date')
                                            ->label('Fecha de Vencimiento'),
                                        Infolists\Components\TextEntry::make('license_type')
                                            ->label('Tipo de Licencia')
                                            ->badge(),
                                    ]),
                                Infolists\Components\Grid::make()
                                    ->columnSpan(3)
                                    ->schema([
                                        PdfViewerEntry::make('file')
                                            ->label('View the PDF')
                                            ->minHeight('40svh')
                                            ->columnSpanFull()
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
