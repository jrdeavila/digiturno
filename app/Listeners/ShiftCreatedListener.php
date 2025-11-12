<?php

namespace App\Listeners;

use App\Events\ShiftCreated;
use App\Jobs\ShiftCancelation;

class ShiftCreatedListener
{
    public function __construct() {}

    public function handle(ShiftCreated $event): void
    {
        ShiftCancelation::dispatch($event->shift)->delay(now()->addMinutes(intval(env('SHIFT_WAITING_TIME', 30))));
    }
}
