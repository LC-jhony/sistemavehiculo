<?php

namespace App\Filament\Resources\DriverLicenseResource\Pages;

use App\Filament\Resources\DriverLicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverLicenses extends ListRecords
{
    protected static string $resource = DriverLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
