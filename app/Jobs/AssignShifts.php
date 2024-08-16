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
        \Illuminate\Support\Facades\DB::beginTransaction();
        $shifts = \App\Models\Shift::whereIn('state', [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred])
            ->whereNull('module_id')
            ->whereDate('created_at', now())
            ->get();

        foreach ($shifts as $shift) {
            $this->searchModuleAttendant($shift);
        }
        \Illuminate\Support\Facades\DB::commit();
    }

    private function searchModuleAttendant(\App\Models\Shift $shift): void
    {
        $attentionProfile = $shift->attentionProfile;
        $room = $shift->room;

        // For Tinker
        // \App\Models\Module::where('attention_profile_id', 1)->where('room_id', 1)->where('enabled', true)->where('status', 'online')->whereHas('attendants', function ($query) { $query->where('status', 'free')->whereDate('module_attendant_accesses.created_at', now()); })->get();

        $availableModules = \App\Models\Module::where('attention_profile_id', $attentionProfile->id)
            ->where('room_id', $room->id)
            ->where('enabled', true)
            ->where('status', \App\Enums\ModuleStatus::Online)
            ->whereHas('attendants', function ($query) {
                // Having pivot table created_at column to get the latest attendant
                $query
                    ->whereIn('status', [
                        \App\Enums\AttendantStatus::Free,
                        // \App\Enums\AttendantStatus::Busy,
                    ])
                    ->whereDate('module_attendant_accesses.created_at', now());
            })
            ->get();

        // Get the module with the least amount of shifts
        $module = $availableModules->sortBy(function ($module) {
            // Sort by shift amount for now and sort by free attendants
            return $module->shifts()->whereDate('created_at', now())->count();
        })->first();


        $shift->module()->associate($module);
        $shift->update([
            'state' => \App\Enums\ShiftState::Pending,
        ]);
    }
}
