<?php

namespace Database\Seeders;

use App\Models\BookedTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookedTable::factory(10)->create();
    }
}
