<?php

namespace App\Observers;

use App\Models\BookedTable;

class BookedTableObserver
{
    /**
     * Handle the BookedTable "created" event.
     */
    public function created(BookedTable $bookedTable): void
    {
        //
    }

    /**
     * Handle the BookedTable "updated" event.
     */
    public function updated(BookedTable $bookedTable): void
    {
        if ($bookedTable->wasChanged('guest_accepted') || $bookedTable->wasChanged('user_accepted')) {
            if (!$bookedTable->guest_accepted || !$bookedTable->user_accepted) {
                $bookedTable->status = 'rejected';
            } elseif ($bookedTable->guest_accepted && $bookedTable->user_accepted) {
                $bookedTable->status = 'accepted';
            }

            $bookedTable->save();
        }
    }

    /**
     * Handle the BookedTable "deleted" event.
     */
    public function deleted(BookedTable $bookedTable): void
    {
        //
    }

    /**
     * Handle the BookedTable "restored" event.
     */
    public function restored(BookedTable $bookedTable): void
    {
        //
    }

    /**
     * Handle the BookedTable "force deleted" event.
     */
    public function forceDeleted(BookedTable $bookedTable): void
    {
        //
    }
}
