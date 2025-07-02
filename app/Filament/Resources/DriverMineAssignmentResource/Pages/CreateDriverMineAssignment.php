<?php

namespace App\Filament\Resources\DriverMineAssignmentResource\Pages;

use Filament\Actions;
use App\Models\DriverMineAssignment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DriverMineAssignmentResource;

/**
 * Página de creación para el recurso DriverMineAssignment
 * 
 * Esta clase maneja la creación de nuevas asignaciones de conductores a minas,
 * incluyendo validaciones para evitar asignaciones duplicadas y notificaciones
 * de éxito o error.
 */
class CreateDriverMineAssignment extends CreateRecord
{
    /**
     * Recurso asociado a esta página
     * 
     * @var string
     */
    protected static string $resource = DriverMineAssignmentResource::class;

    /**
     * Define la URL de redirección después de crear un registro
     * 
     * Redirige al usuario a la página de índice del recurso después
     * de crear exitosamente una asignación.
     * 
     * @return string URL de redirección
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Hook que se ejecuta antes de crear el registro
     * 
     * Valida que no exista una asignación duplicada para el mismo conductor
     * en el mismo período (año y mes). Si encuentra una asignación existente,
     * muestra una notificación de error y detiene el proceso de creación.
     * 
     * @return void
     * @throws \Exception Si existe una asignación duplicada
     */
    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        
        // Validar conflictos de horarios
        $this->validateScheduleConflicts($data);
        
        // Validar capacidad del vehículo
        $this->validateVehicleCapacity($data);
        
        // Validar licencias del conductor
        $this->validateDriverLicenses($data);
    }

    private function validateScheduleConflicts(array $data): void
    {
        $conflicts = DriverMineAssignment::where('driver_id', $data['driver_id'])
            ->where('status', 'active')
            ->whereBetween('start_date', [$data['start_date'], $data['end_date']])
            ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
            ->exists();
        
        if ($conflicts) {
            Notification::make()
                ->title('Conflicto de Horarios')
                ->body('El conductor ya tiene asignaciones en el período seleccionado.')
                ->warning()
                ->send();
            $this->halt();
        }
    }

    /**
     * Hook que se ejecuta después de crear el registro exitosamente
     * 
     * Muestra una notificación de éxito confirmando que la asignación
     * ha sido creada correctamente.
     * 
     * @return void
     */
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Asignación Creada')
            ->body('La asignación ha sido creada exitosamente.')
            ->success()
            ->send();
    }
}
