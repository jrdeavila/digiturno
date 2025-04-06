<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = \App\Models\Shift::whereIn(
            'state',
            [
                \App\Enums\ShiftState::Pending,
                \App\Enums\ShiftState::PendingTransferred,
            ]

        )->orderBy('created_at', 'asc')->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }

    public function store(\App\Http\Requests\ShiftRequest $request)
    {
        $shift = $request->createShift();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function updateShiftWithAttentionProfile(\App\Models\Shift $shift, \App\Http\Requests\ShiftWithAttentionProfileRequest $request)
    {
        $shiftUpdated = $request->updateShift($shift);
        return new \App\Http\Resources\ShiftResource($shiftUpdated);
    }


    public function show(\App\Models\Shift $shift)
    {
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function destroy(\App\Models\Shift $shift)
    {
        throw_unless($shift->state !== \App\Enums\ShiftState::InProgress, \App\Exceptions\ShiftInProgressDeletingFailedException::class);
        \App\Jobs\DeleteShift::dispatch($shift);
        return response()->json(null, 204);
    }

    public function createShiftWithAttentionProfile(\App\Http\Requests\ShiftWithAttentionProfileRequest $request)
    {
        $shift = $request->createShift();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function distracted()
    {
        $shift =  \App\Models\Shift::where(
            'state',
            \App\Enums\ShiftState::Distracted
        )->latest()->get();
        return \App\Http\Resources\ShiftResource::collection($shift);
    }

    public function inProgress()
    {
        $shift =  \App\Models\Shift::where(
            'state',
            \App\Enums\ShiftState::InProgress
        )->latest()->get();
        return \App\Http\Resources\ShiftResource::collection($shift);
    }

    public function completedShift(\App\Models\Shift $shift, \App\Http\Requests\CompletedShiftRequest $request)
    {
        $shift = $request->completedShift($shift);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function qualifiedShift(\App\Models\Shift $shift, \App\Http\Requests\QualifiedShiftRequest $request)
    {
        $request->createQualification();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function distractedShift(\App\Models\Shift $shift, Request $request)
    {
        $module = $request->module;
        $shift->update(['state' => \App\Enums\ShiftState::Distracted, 'module_id' => $module->id]);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function transferShift(\App\Models\Shift $shift, \App\Http\Requests\TransferShiftRequest $request)
    {
        $shiftTransferred = $request->transferShift($shift);
        return new \App\Http\Resources\ShiftResource($shiftTransferred);
    }

    public function sendToPending(\App\Models\Shift $shift)
    {
        $shift->update(['state' => \App\Enums\ShiftState::Pending]);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function call(\App\Models\Shift $shift)
    {
        DB::beginTransaction();
        if ($shift->module != null) {
            throw new \App\Exceptions\ShiftAlreadyAssignedException();
        }
        $module = request()->module;
        $shift->update([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id,
        ]);

        $module->currentAttendant()?->update([
            'status' => "busy",
        ]);
        \App\Events\CallClient::dispatch($shift, $module);
        DB::commit();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function sendToInProgress(\App\Models\Shift $shift, \App\Http\Requests\SendToInProgressRequest $request)
    {
        $shiftInProgress = $request->sendToInProgress($shift);
        return new \App\Http\Resources\ShiftResource($shiftInProgress);
    }
}
