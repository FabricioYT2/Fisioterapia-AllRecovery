<?php

namespace App\Filament\Resources\CompraMaterialResource\Pages;

use App\Filament\Resources\CompraMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompraMaterial extends CreateRecord
{
    protected static string $resource = CompraMaterialResource::class;
    protected function afterSave(): void
{
    $record = $this->record;
    if ($record) {
        \App\Filament\Resources\CompraMaterialResource::procesarCompra($record);
    }
}
}
