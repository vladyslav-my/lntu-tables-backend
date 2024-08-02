<?php

namespace App\Console\Commands\BookedTable;

use App\Models\BookedTable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStatusBasedOnTime extends Command
{
    protected $signature = 'status:update';
    protected $description = 'Update status based on time range';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();

        BookedTable
            ::where('time_from', '<=', $now)
            ->where('time_to', '>=', $now)
            ->update(['status' => 'during']);

        BookedTable
            ::where('time_to', '<', $now)
            ->update(['status' => 'timeout']);

        return Command::SUCCESS;
    }
}
