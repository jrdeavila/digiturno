<?php

namespace App\Listeners;

use App\Enums\ShiftState;
use App\Events\ShiftCreated;
use App\Jobs\ShiftCancelation;
use App\Models\ShiftHistory;
use Illuminate\Support\Facades\Auth;

class ShiftCreatedListener
{
    public function __construct() {}

    public function handle(ShiftCreated $event): void
    {
        ShiftHistory::create([
            'shift_id' => $event->shift->id,
            'state' => ShiftState::Pending->value,
            'responsable_id' => Auth::id(),
        ]);
        ShiftCancelation::dispatch($event->shift)->delay(now()->addMinutes(intval(env('SHIFT_WAITING_TIME', 30))));
    }
}
