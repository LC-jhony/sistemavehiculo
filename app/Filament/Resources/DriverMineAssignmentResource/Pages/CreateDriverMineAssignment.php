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
        // Verificar si existe cualquier asignación para el mismo conductor en el mismo período
        $existingAssignment = DriverMineAssignment::where('driver_id', $this->data['driver_id'])
            ->where('year', $this->data['year'])
            ->where('month', $this->data['month'])
            ->with(['driver', 'mine']) // Cargar relaciones para mostrar información detallada
            ->first();

        if ($existingAssignment) {
            // Determinar el mensaje de estado basado en el estado de la asignación existente
            $statusMessage = match ($existingAssignment->status) {
                'Activo' => 'activa',
                'Completedo' => 'completada', // Nota: Posible error tipográfico en 'Completedo'
                'Cancelado' => 'cancelada',
                default => 'existente'
            };

            // Obtener información detallada para el mensaje de error
            $driverName = $existingAssignment->driver->full_name;
            $mineName = $existingAssignment->mine->name;
            $period = $existingAssignment->month_name . ' ' . $existingAssignment->year;

            // Mostrar notificación de error con información específica
            Notification::make()
                ->title('Error de Asignación')
                ->body("El conductor {$driverName} ya tiene una asignación {$statusMessage} para la mina {$mineName} en el período {$period}. Debe modificar o eliminar la asignación existente antes de crear una nueva.")
                ->danger()
                ->send();

            // Detener el proceso de creación
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
