<?php

namespace App\Filament\Resources\IngresoEgresoResource\Pages;

use App\Filament\Resources\IngresoEgresoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIngresoEgreso extends ViewRecord
{
    protected static string $resource = IngresoEgresoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}