<?php

namespace App\Filament\Resources\CompraMaterialResource\Pages;

use App\Filament\Resources\CompraMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompraMaterial extends EditRecord
{
    protected static string $resource = CompraMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
