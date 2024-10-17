<?php

namespace App\Filament\Admin\Resources\BookedTableResource\Pages;

use App\Filament\Admin\Resources\BookedTableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookedTable extends CreateRecord
{
    protected static string $resource = BookedTableResource::class;
}
