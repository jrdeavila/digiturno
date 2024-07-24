<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomShiftController extends Controller
{
    public function index(\App\Models\Room $room, \App\Models\AttentionProfile $attentionProfile)
    {
        $shifts = $room->shifts()->where(
            'state',
            \App\Enums\ShiftState::Pending,
        )
            ->where('attention_profile_id', $attentionProfile->id)
            ->orderBy('created_at', 'asc')->get();
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
