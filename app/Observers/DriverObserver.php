<?php

namespace App\Observers;

use App\Models\Driver;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class DriverObserver
{
    /**
     * Handle the Driver "created" event.
     */
    public function created(Driver $driver): void
    {
        $observer = Auth::user();
        Notification::make()
            ->title('Nuevo conductor creado')
            ->body("El conductor {$driver->full_name} ha sido creado por {$observer->name}")
            ->success()
            ->sendToDatabase($observer);
    }

    /**
     * Handle the Driver "updated" event.
     */
    public function updated(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "deleted" event.
     */
    public function deleted(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "restored" event.
     */
    public function restored(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "force deleted" event.
     */
    public function forceDeleted(Driver $driver): void
    {
        //
    }
}
