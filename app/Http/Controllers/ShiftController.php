<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = \App\Models\Shift::where(
            'state',
            \App\Enums\ShiftState::Pending
        )->orderBy('created_at', 'asc')->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }

    public function store(\App\Http\Requests\ShiftRequest $request)
    {
        $shift = $request->createShift();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function show(\App\Models\Shift $shift)
    {
        return new \App\Http\Resources\ShiftResource($shift);
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

    public function completedShift(\App\Models\Shift $shift)
    {
        $shift->update(['state' => \App\Enums\ShiftState::Completed]);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function qualifiedShift(\App\Models\Shift $shift, \App\Http\Requests\QualifiedShiftRequest $request)
    {
        $request->createQualification();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function distractedShift(\App\Models\Shift $shift)
    {
        $shift->update(['state' => \App\Enums\ShiftState::Distracted]);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function transferShift(\App\Models\Shift $shift, \App\Http\Requests\TransferShiftRequest $request)
    {
        $request->transferShift();
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function sendToPending(\App\Models\Shift $shift)
    {
        $shift->update(['state' => \App\Enums\ShiftState::Pending]);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function call(\App\Models\Shift $shift)
    {
        \App\Events\CallClient::dispatch($shift);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function sendToInProgress(\App\Models\Shift $shift, \App\Http\Requests\SendToInProgressRequest $request)
    {
        $shiftInProgress = $request->sendToInProgress($shift);
        return new \App\Http\Resources\ShiftResource($shiftInProgress);
    }
}
