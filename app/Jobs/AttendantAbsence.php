<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttendantAbsence implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public \App\Models\Attendant $attendant;
    /**
     * Create a new job instance.
     */
    public function __construct(
        \App\Models\Attendant $attendant
    ) {
        $this->attendant = $attendant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->attendant->update([
            'status' => \App\Enums\AttendantStatus::Absent
        ]);
    }
}
