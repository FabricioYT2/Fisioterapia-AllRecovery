<?php

namespace App\Filament\Resources\IngresoEgresoResource\Pages;

use App\Filament\Resources\IngresoEgresoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIngresoEgreso extends EditRecord
{
    protected static string $resource = IngresoEgresoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}