<?php

namespace App\Filament\Resources\MineResource\Pages;

use App\Filament\Resources\MineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMines extends ListRecords
{
    protected static string $resource = MineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
