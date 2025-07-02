<?php

namespace App\Filament\Resources\MineResource\Pages;

use Filament\Actions;
use App\Filament\Resources\MineResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMine extends CreateRecord
{
    protected static string $resource = MineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Mina creada exitosamente';
    // }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Mina creada exitosamente')
            ->body('La mina ha sido creada exitosamente');
    }
}
