<?php

namespace App\Filament\Admin\Resources\BookedTableResource\Pages;

use App\Filament\Admin\Resources\BookedTableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookedTable extends EditRecord
{
    protected static string $resource = BookedTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
