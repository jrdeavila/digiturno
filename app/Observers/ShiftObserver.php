<?php

namespace App\Observers;

class ShiftObserver
{
    public function created(\App\Models\Shift $shift)
    {
        \App\Events\ShiftCreated::dispatch($shift);
    }

    public function updated(\App\Models\Shift $shift)
    {
        \App\Events\ShiftUpdated::dispatch($shift);
    }
}
