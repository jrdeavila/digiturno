<?php

namespace App\Jobs;

use App\Enums\ShiftState;
use App\Models\Shift;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class AfterCalledShift implements ShouldQueue
{
    use Queueable;


    public function __construct(private Shift $shift) {}


    public function handle(): void
    {
        DB::beginTransaction();
        if ($this->shift->state === ShiftState::Called->value) {
            $this->shift->state = ShiftState::Pending->value;
        }
        $this->shift->save();
        DB::commit();
    }
}
