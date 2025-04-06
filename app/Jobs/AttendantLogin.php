<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttendantLogin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public \App\Models\Attendant $attendant;
    public \App\Models\Module $module;
    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\Attendant $attendant, \App\Models\Module $module)
    {
        $this->attendant = $attendant;
        $this->module = $module;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \App\Models\ModuleAttendantAccess::create([
            'module_id' => $this->module->id,
            'attendant_id' => $this->attendant->id,
        ]);

        $this->module->update([
            'status' => \App\Enums\ModuleStatus::Online
        ]);

        $this->attendant->update([
            'status' => \App\Enums\AttendantStatus::Free
        ]);
    }
}
