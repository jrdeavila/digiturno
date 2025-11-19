<?php

namespace App\Observers;

use App\Events\ShiftCreated;
use App\Events\ShiftDeleted;
use App\Events\ShiftUpdated;

class ShiftObserver
{
    public function created(\App\Models\Shift $shift)
    {
        ShiftCreated::dispatch($shift);
    }

    public function updated(\App\Models\Shift $shift)
    {
        ShiftUpdated::dispatch($shift);
    }

    public function deleted(\App\Models\Shift $shift)
    {
        ShiftDeleted::dispatch($shift);
    }
}
