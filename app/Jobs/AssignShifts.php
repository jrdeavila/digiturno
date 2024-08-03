<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignShifts implements ShouldQueue
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
            ->get();

        foreach ($shifts as $shift) {
            $this->searchModuleAttendant($shift);
        }
    }

    private function searchModuleAttendant(\App\Models\Shift $shift): void
    {
        $attentionProfile = $shift->attentionProfile;
        $room = $shift->room;
        $module = \App\Models\Module::where('attention_profile_id', $attentionProfile->id)
            ->where('room_id', $room->id)
            ->where('enabled', true)
            ->where('status', \App\Enums\ModuleStatus::Online)
            // Having last attendant
            ->whereHas('attendants', function ($query) {
                $query->where('status', \App\Enums\AttendantStatus::Free);
            })
            ->first();

        $shift->module()->associate($module);
        $shift->update([
            'state' => \App\Enums\ShiftState::InProgress,
        ]);
        \App\Jobs\ShiftInProgress::dispatch($shift);
        event(new \App\Events\CallClient($shift));
    }
}
