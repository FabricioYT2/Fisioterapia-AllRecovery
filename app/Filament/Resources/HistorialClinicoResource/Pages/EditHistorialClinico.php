<?php

namespace App\Filament\Resources\HistorialClinicoResource\Pages;

use App\Filament\Resources\HistorialClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorialClinico extends EditRecord
{
    protected static string $resource = HistorialClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
