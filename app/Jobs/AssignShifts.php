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
            ->whereNull('module_id')
            ->get();

        foreach ($shifts as $shift) {
            $this->searchModuleAttendant($shift);
        }
    }

    private function searchModuleAttendant(\App\Models\Shift $shift): void
    {
        $attentionProfile = $shift->attentionProfile;
        $room = $shift->room;
        $availableModules = \App\Models\Module::where('attention_profile_id', $attentionProfile->id)
            ->where('room_id', $room->id)
            ->where('enabled', true)
            ->where('status', \App\Enums\ModuleStatus::Online)
            ->whereHas('attendants', function ($query) {
                $query->whereNotIn('status', [
                    \App\Enums\AttendantStatus::Absent,
                    \App\Enums\AttendantStatus::Busy,
                ]);
            })
            ->get();

        // Get the module with the least amount of shifts
        $module = $availableModules->sortBy(function ($module) {
            return $module->shifts()->whereIn('state', [
                \App\Enums\ShiftState::Pending,
                \App\Enums\ShiftState::PendingTransferred,
                \App\Enums\ShiftState::InProgress,
                \App\Enums\ShiftState::Qualified,
                \App\Enums\ShiftState::Completed,
            ])->count();
        })->first();


        $shift->module()->associate($module);
        $shift->update([
            'state' => \App\Enums\ShiftState::Pending,
        ]);
    }
}
