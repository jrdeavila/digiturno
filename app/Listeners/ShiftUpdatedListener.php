<?php

namespace App\Listeners;

use App\Events\ShiftUpdated;
use App\Models\ShiftHistory;
use Illuminate\Support\Facades\Auth;

class ShiftUpdatedListener
{

    public function __construct() {}

    public function handle(ShiftUpdated $event): void
    {
        ShiftHistory::create([
            'shift_id' => $event->shift->id,
            'state' => $event->shift->state,
            'responsable_id' => Auth::id(),
        ]);
    }
}
