<?php

namespace App\Jobs;

use App\Enums\ModuleStatus;
use App\Models\AttentionProfile;
use App\Models\Module;
use App\Models\Room;
use App\Models\Shift;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ShiftTransferred implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public Shift $shift;
    public AttentionProfile $attentionProfile;
    /**
     * Create a new job instance.
     */

    public function __construct(
        Shift $shift,
        AttentionProfile $attentionProfile
    ) {
        $this->shift = $shift;
        $this->attentionProfile = $attentionProfile;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $this->createShift($this->shift);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    private function searchModule(int $roomId): Module
    {
        $room = Room::find($roomId);
        $module = $room->modules()
            ->whereHas('attentionProfiles', function ($query) {
                $query->where('attention_profiles.id', $this->attentionProfile->id);
            })
            ->where('status', ModuleStatus::Online)
            ->withCount('pendingShifts')
            ->orderBy('pending_shifts_count', 'asc')
            ->firstOrFail();

        return $module;
    }

    private function createShift(Shift $shift)
    {
        $client = $shift->client;

        $exists = Shift::where('client_id', $client->id)
            ->current()->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'El cliente ya tiene un turno pendiente');
        }
        $module = $this->searchModule($shift->room_id);
        Shift::create([
            'attention_profile_id' => $this->attentionProfile->id,
            'client_id' => $client->id,
            'room_id' => $shift->room_id,
            'module_id' => $module->id,
        ]);
    }
}
