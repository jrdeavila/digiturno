<?php

namespace App\Observers;

class AttendantObserver
{
    public function updated(\App\Models\Attendant $attendant)
    {
        \App\Events\AttendantUpdated::dispatch($attendant);
    }
}
