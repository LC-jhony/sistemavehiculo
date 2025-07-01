<?php

namespace App\Filament\Imports;

use App\Models\Cargo;
use App\Models\Driver;
use App\Observers\DriverObserver;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

use Filament\Notifications\DatabaseNotification;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(DriverObserver::class)]
class DriverImporter extends Importer
{
    protected static ?string $model = Driver::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('last_paternal_name'),
            ImportColumn::make('last_maternal_name'),
            ImportColumn::make('dni'),
            ImportColumn::make('cargo_name')
                ->label('Nombre del Cargo')
                ->rules(['string', 'exists:cargos,name'])
                ->example('Conductor')
                ->fillRecordUsing(function (Driver $record, $state) {
                    $cargo = Cargo::where('name', $state)->first();
                    $record->cargo_id = $cargo?->id;
                }),
            // ImportColumn::make('file'),
            // ImportColumn::make('status'),

        ];
    }

    public function resolveRecord(): ?Driver
    {
        // return Driver::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Driver();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your driver import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
        DatabaseNotifications::pollingInterval('30s');
    }
}
