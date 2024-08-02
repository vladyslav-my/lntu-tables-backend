<?php

namespace App\Http\Controllers;

use App\Models\Table;

class TableController extends Controller
{
    public function index(Table $table)
    {
        $tables = $table->all();
        
        $items = collect($tables)->map(function ($table) {
            return [
                'id' => $table->id,
                'number' => $table->number,
            ];
        });

        return response()->json($items);
    }
}
