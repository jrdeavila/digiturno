<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ModuleShiftController extends Controller
{
    public function currentShift(Request $request)
    {
        $module =  $request->module;

        $shift = \App\Models\Shift::where('module_id', $module->id)
            // Where state is in progress or completed
            ->whereIn('state', [\App\Enums\ShiftState::InProgress, \App\Enums\ShiftState::Completed])
            ->whereDate('shifts.created_at', now())
            ->first();

        if (!$shift) {
            return response()->json(null, 204);
        }
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function myShifts(Request $request)
    {
        $module = $request->module;
        $clientType = $module->clientType;
        $priority = $clientType->priority; // 1: preferential ,2: processor ,3: standard
        $shifts = \App\Models\Shift::where('module_id', $module->id)
            ->where(function ($query) use ($priority) {
                // If priority is 1, get the only shifts with processor client type else get all shifts
                if ($priority === 2) {
                    // Get shift client  relationship
                    $query->whereHas('client', function ($query) {
                        // Get client type relationship
                        $query->whereHas('clientType', function ($query) {
                            $query->where('priority', 2);
                        });
                    });
                }
                return $query;
            })
            ->whereIn('state', [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred])
            ->whereDate('shifts.created_at', now())
            ->get();

        return \App\Http\Resources\ShiftResource::collection($shifts);
    }
}
