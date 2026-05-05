<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaciente extends ViewRecord
{
    protected static string $resource = PacienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->before(fn($record) => throw_if($record->citas()->exists(), new \Exception('No se puede eliminar: tiene citas.'))),
        ];
    }
}