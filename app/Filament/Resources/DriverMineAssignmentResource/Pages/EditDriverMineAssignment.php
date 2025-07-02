<?php

namespace App\Filament\Resources\DriverMineAssignmentResource\Pages;

use App\Filament\Resources\DriverMineAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriverMineAssignment extends EditRecord
{
    protected static string $resource = DriverMineAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
