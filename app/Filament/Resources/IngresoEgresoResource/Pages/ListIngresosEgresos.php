<?php

namespace App\Filament\Resources\IngresoEgresoResource\Pages;

use App\Filament\Resources\IngresoEgresoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIngresosEgresos extends ListRecords
{
    protected static string $resource = IngresoEgresoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Movimiento'),
        ];
    }
}