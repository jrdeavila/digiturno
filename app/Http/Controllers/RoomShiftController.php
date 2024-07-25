<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomShiftController extends Controller
{
    public function shiftsByRoom(\App\Models\Room $room)
    {
        $shifts = $room->shifts()->whereIn(
            'state',
            [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred],
        )
            ->orderBy('created_at', 'desc')
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }

    public function shiftsDistractedByRoom(\App\Models\Room $room)
    {
        $shifts = $room->shifts()->where(
            'state',
            \App\Enums\ShiftState::Distracted
        )
            ->latest()->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }


    public function index(\App\Models\Room $room, \App\Models\AttentionProfile $attentionProfile)

    {
        $shifts = $room->shifts()->whereIn(
            'state',
            [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred],
        )
            ->where('attention_profile_id', $attentionProfile->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }


    public function distracted(\App\Models\Room $room, \App\Models\AttentionProfile $attentionProfile)
    {
        $shift =  $room->shifts()->where(
            'state',
            \App\Enums\ShiftState::Distracted
        )
            ->where('attention_profile_id', $attentionProfile->id)
            ->latest()->get();
        return \App\Http\Resources\ShiftResource::collection($shift);
    }
}
