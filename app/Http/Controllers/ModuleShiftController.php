<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ModuleShiftController extends Controller
{
    public function currentShift(\App\Models\Module $module)
    {
        $shift = \App\Models\Shift::where('module_id', $module->id)
            // Where state is in progress or completed
            ->whereIn('state', [\App\Enums\ShiftState::InProgress, \App\Enums\ShiftState::Completed])
            ->first();

        if (!$shift) {
            return response()->json(null, 204);
        }
        return new \App\Http\Resources\ShiftResource($shift);
    }
}
