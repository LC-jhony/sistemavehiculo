<?php

namespace App\Services;

use Filament\Notifications\Notification;

class NotificationService
{
    public static function assignmentConflict(string $driverName, string $mineName, string $period): void
    {
        Notification::make()
            ->title('⚠️ Conflicto de Asignación')
            ->body("El conductor **{$driverName}** ya está asignado a **{$mineName}** en **{$period}**")
            ->warning()
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver Asignación')
                    ->url(route('filament.admin.resources.driver-mine-assignments.index')),
                \Filament\Notifications\Actions\Action::make('edit')
                    ->label('Modificar')
                    ->color('primary')
            ])
            ->persistent()
            ->send();
    }
    
    public static function maintenanceReminder(Vehicle $vehicle): void
    {
        Notification::make()
            ->title('🔧 Mantenimiento Requerido')
            ->body("El vehículo {$vehicle->plate} requiere mantenimiento")
            ->warning()
            ->send();
    }
}