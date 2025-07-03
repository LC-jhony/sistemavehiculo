<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Imports\DriverImporter;
use App\Filament\Resources\DriverResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-squares-plus'),
            Actions\ImportAction::make('import')
                ->importer(DriverImporter::class)
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn() => auth()->user()->hasAnyRole(['super_admin', 'admin']))
        ];
    }
}
