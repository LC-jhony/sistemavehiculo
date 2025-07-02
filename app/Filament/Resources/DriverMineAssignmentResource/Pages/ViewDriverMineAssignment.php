<?php

namespace App\Filament\Resources\DriverMineAssignmentResource\Pages;

use App\Filament\Resources\DriverMineAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDriverMineAssignment extends ViewRecord
{
    protected static string $resource = DriverMineAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
