<?php

namespace App\Jobs;

use App\Enums\ShiftState;
use App\Models\Shift;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ShiftCancelation implements ShouldQueue
{
    use Queueable;

    public function __construct(private Shift $shift) {}


    public function handle(): void
    {
        if (
            $this->shift->state === ShiftState::Pending
            || $this->shift->state === ShiftState::PendingTransferred
            || $this->shift->state === ShiftState::Distracted
        ) {

            $this->shift->value = ShiftState::Cancelled;
            $this->shift->save();
        }
    }
}
