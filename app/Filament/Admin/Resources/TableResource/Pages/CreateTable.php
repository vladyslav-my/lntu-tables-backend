<?php

namespace App\Filament\Admin\Resources\TableResource\Pages;

use App\Filament\Admin\Resources\TableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTable extends CreateRecord
{
    protected static string $resource = TableResource::class;
}
