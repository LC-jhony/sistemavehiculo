<?php

namespace App\Tables\Columns;

use Carbon\Carbon;
use Filament\Tables\Columns\Column;

class StatusColumn extends Column
{
    protected string $view = 'tables.columns.status-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->state(function ($record) {
            if (! $record->expiration_date) {
                return [
                    'status' => 'sin-fecha',
                    'label' => 'Sin fecha',
                    'color' => 'gray',
                    'icon' => 'heroicon-o-question-mark-circle',
                    'bg_color' => 'bg-gray-100',
                    'text_color' => 'text-gray-800',
                    'dark_bg' => 'dark:bg-gray-800',
                    'dark_text' => 'dark:text-gray-200',
                ];
            }

            $expirationDate = Carbon::parse($record->expiration_date);
            $today = Carbon::today();
            $daysUntilExpiration = $today->diffInDays($expirationDate, false);

            if ($daysUntilExpiration < 0) {
                // Vencido - Rojo intenso
                return [
                    'status' => 'vencido',
                    'label' => 'VENCIDO',
                    'color' => 'red',
                    'icon' => 'heroicon-o-x-circle',
                    'bg_color' => 'bg-red-500',
                    'text_color' => 'text-white',
                    'dark_bg' => 'dark:bg-red-600',
                    'dark_text' => 'dark:text-white',
                    'days' => abs($daysUntilExpiration),
                    'pulse' => true,
                ];
            } elseif ($daysUntilExpiration <= 7) {
                // Crítico - Rojo/Naranja (vence en 7 días o menos)
                return [
                    'status' => 'critico',
                    'label' => 'CRÍTICO',
                    'color' => 'orange',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'bg_color' => 'bg-orange-500',
                    'text_color' => 'text-white',
                    'dark_bg' => 'dark:bg-orange-600',
                    'dark_text' => 'dark:text-white',
                    'days' => $daysUntilExpiration,
                    'pulse' => true,
                ];
            } elseif ($daysUntilExpiration <= 30) {
                // Por vencer - Amarillo
                return [
                    'status' => 'por-vencer',
                    'label' => 'Por vencer',
                    'color' => 'yellow',
                    'icon' => 'heroicon-o-clock',
                    'bg_color' => 'bg-yellow-400',
                    'text_color' => 'text-yellow-900',
                    'dark_bg' => 'dark:bg-yellow-500',
                    'dark_text' => 'dark:text-yellow-900',
                    'days' => $daysUntilExpiration,
                ];
            } elseif ($daysUntilExpiration <= 90) {
                // Próximo a vencer - Azul
                return [
                    'status' => 'proximo',
                    'label' => 'Próximo',
                    'color' => 'blue',
                    'icon' => 'heroicon-o-information-circle',
                    'bg_color' => 'bg-blue-500',
                    'text_color' => 'text-white',
                    'dark_bg' => 'dark:bg-blue-600',
                    'dark_text' => 'dark:text-white',
                    'days' => $daysUntilExpiration,
                ];
            } else {
                // Vigente - Verde
                return [
                    'status' => 'vigente',
                    'label' => 'Vigente',
                    'color' => 'green',
                    'icon' => 'heroicon-o-check-circle',
                    'bg_color' => 'bg-green-500',
                    'text_color' => 'text-white',
                    'dark_bg' => 'dark:bg-green-600',
                    'dark_text' => 'dark:text-white',
                    'days' => $daysUntilExpiration,
                ];
            }
        });
    }
}
