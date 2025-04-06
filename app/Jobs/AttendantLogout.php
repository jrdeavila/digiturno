<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttendantLogout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public \App\Models\Attendant $attendant;
    public \App\Models\Module $module;
    public function __construct(
        \App\Models\Attendant $attendant,
        \App\Models\Module $module
    ) {
        $this->attendant = $attendant;
        $this->module = $module;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->attendant->update([
            'status' => \App\Enums\AttendantStatus::Offline
        ]);
        $this->module->update([
            'status' => \App\Enums\ModuleStatus::Offline
        ]);
    }
}
