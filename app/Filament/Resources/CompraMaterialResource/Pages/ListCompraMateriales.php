<?php

namespace App\Filament\Resources\CompraMaterialResource\Pages;

use App\Filament\Resources\CompraMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompraMateriales extends ListRecords
{
    protected static string $resource = CompraMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}