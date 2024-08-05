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
        $this->shift->refresh();
        $module = $this->shift->module;
        if (!$module) return;
        if (!$module->status == \App\Enums\ModuleStatus::Offline) return;
        $attendant = $module->attendants->last();
        $attendant->update([
            'status' => \App\Enums\AttendantStatus::Free
        ]);
    }
}