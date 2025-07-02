<?php

namespace App\Filament\Resources\DriverMineAssignmentResource\Pages;

use App\Filament\Resources\DriverMineAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDriverMineAssignments extends ListRecords
{
    protected static string $resource = DriverMineAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(fn() => $this->getModel()::count()),

            'active' => Tab::make('Activas')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => $this->getModel()::where('status', 'active')->count()),

            'current_month' => Tab::make('Mes Actual')
                ->modifyQueryUsing(fn(Builder $query) => $query->currentMonth())
                ->badge(fn() => $this->getModel()::currentMonth()->count()),

            'completed' => Tab::make('Completadas')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => $this->getModel()::where('status', 'completed')->count()),
        ];
    }
}
