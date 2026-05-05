<?php

namespace App\Filament\Resources\HistorialClinicoResource\Pages;

use App\Filament\Resources\HistorialClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorialClinicos extends ListRecords
{
    protected static string $resource = HistorialClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
