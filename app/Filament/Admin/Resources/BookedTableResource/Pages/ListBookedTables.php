<?php

namespace App\Filament\Admin\Resources\BookedTableResource\Pages;

use App\Filament\Admin\Resources\BookedTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookedTables extends ListRecords
{
    protected static string $resource = BookedTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
