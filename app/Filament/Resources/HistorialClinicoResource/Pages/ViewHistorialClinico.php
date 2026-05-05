<?php

namespace App\Filament\Resources\HistorialClinicoResource\Pages;

use App\Filament\Resources\HistorialClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHistorialClinico extends ViewRecord
{
    protected static string $resource = HistorialClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}