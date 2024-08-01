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
            ->join("clients", "shifts.client_id", "=", "clients.id")
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->orderBy('shifts.created_at', 'asc')
            ->orderBy('client_types.priority', 'asc')
            ->select('shifts.*')
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }

    public function shiftsDistractedByRoom(\App\Models\Room $room)
    {
        $shifts = $room->shifts()->where(
            'state',
            \App\Enums\ShiftState::Distracted
        )
            ->join("clients", "shifts.client_id", "=", "clients.id")
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->orderBy('shifts.created_at', 'asc')
            ->orderBy('client_types.priority', 'asc')
            ->select('shifts.*')
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }


    public function index(\App\Models\Room $room, \App\Models\AttentionProfile $attentionProfile)

    {
        $shifts = $room->shifts()->whereIn(
            'state',
            [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred],
        )
            ->where('attention_profile_id', $attentionProfile->id)
            ->join("clients", "shifts.client_id", "=", "clients.id")
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->orderBy('shifts.created_at', 'asc')
            ->orderBy('client_types.priority', 'asc')
            ->select('shifts.*')
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
            ->join("clients", "shifts.client_id", "=", "clients.id")
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->orderBy('shifts.created_at', 'asc')
            ->orderBy('client_types.priority', 'asc')
            ->select('shifts.*')
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shift);
    }
}
