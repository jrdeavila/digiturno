<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyRepeatedShifts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shifts = \App\Models\Shift::whereIn('state', [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred])
            ->whereDate('created_at', now())
            ->get();
        foreach ($shifts as $shift) {
            $this->verifyRepeatedShifts($shift);
        }
    }

    public function verifyRepeatedShifts(\App\Models\Shift $shift): void
    {
        // Transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        // Get shifts that have the same client and room
        $shifts = \App\Models\Shift::where('client_id', $shift->client_id)
            ->where('room_id', $shift->room_id)
            ->whereDate('created_at', now())
            ->get();
        // Delete all shifts that are not the first one
        foreach ($shifts as $shift) {
            if ($shift->id !== $shifts->first()->id) {
                $shift->delete();
            }
        }
        \Illuminate\Support\Facades\DB::commit();
    }
}
