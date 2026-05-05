<?php

namespace App\Filament\Resources\CompraMaterialResource\Pages;

use App\Filament\Resources\CompraMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompraMaterial extends ViewRecord
{
    protected static string $resource = CompraMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}