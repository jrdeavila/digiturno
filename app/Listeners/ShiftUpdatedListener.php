<?php

namespace App\Listeners;

use App\Enums\ShiftState;
use App\Events\ShiftUpdated;
use App\Jobs\AfterCalledShift;
use App\Models\ShiftHistory;
use Illuminate\Support\Facades\Auth;

class ShiftUpdatedListener
{

    public function __construct() {}

    public function handle(ShiftUpdated $event): void
    {
        if ($event->shift->state === ShiftState::Called) {
            $job = new AfterCalledShift($event->shift);
            dispatch($job)->delay(now()->addSeconds(10));
        }
        ShiftHistory::create([
            'shift_id' => $event->shift->id,
            'state' => $event->shift->state,
            'responsable_id' => Auth::id(),
        ]);
    }
}
