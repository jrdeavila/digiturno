<?php

namespace App\Listeners;

use App\Events\ShiftCreated;
use App\Jobs\ProgramShiftCancelation;
use App\Jobs\ShiftCancelation;

class ShiftCreatedListener
{

    public function __construct() {}


    public function handle(ShiftCreated $event): void
    {
        ShiftCancelation::dispatch($event->shift)->delay(now()->addMinutes(30));
    }
}
