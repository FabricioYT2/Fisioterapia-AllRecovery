<?php

namespace App\Filament\Resources\CobroResource\Pages;

use App\Filament\Resources\CobroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCobros extends ListRecords
{
    protected static string $resource = CobroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
