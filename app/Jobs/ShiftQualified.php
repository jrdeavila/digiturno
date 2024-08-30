<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ShiftQualified implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public \App\Models\Shift $shift;

    public function __construct(
        \App\Models\Shift $shift
    ) {
        $this->shift = $shift;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $this->shift->update([
            'state' => 'qualified',
        ]);
        $this->shift->module?->currentAttendant()?->update([
            'status' => 'free',
        ]);
        \Illuminate\Support\Facades\DB::commit();
    }
}
