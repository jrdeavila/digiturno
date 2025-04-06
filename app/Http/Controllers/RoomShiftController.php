<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomShiftController extends Controller
{
    public function shiftsByRoom(\App\Models\Room $room)
    {
        // Get only shift in today date
        $shifts = $room->shifts()->whereIn(
            'state',
            [
                \App\Enums\ShiftState::Pending,
                \App\Enums\ShiftState::PendingTransferred,
                \App\Enums\ShiftState::Distracted,
                \App\Enums\ShiftState::Distracted,
                \App\Enums\ShiftState::Transferred,
                \App\Enums\ShiftState::InProgress,
                \App\Enums\ShiftState::Completed,
                \App\Enums\ShiftState::Qualified,
            ],
        )
            ->join("clients", "shifts.client_id", "=", "clients.id")
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->orderBy('shifts.created_at', 'asc')
            ->orderBy('client_types.priority', 'asc')
            ->select('shifts.*')
            ->whereDate('shifts.created_at', now())
            ->get();
        return \App\Http\Resources\ShiftResource::collection($shifts);
    }

    public function shiftsDistractedByRoom(\App\Models\Room $room)
    {
        $shifts = $room->shifts()->where(
            'state',
            \App\Enums\ShiftState::Distracted
        )
            ->whereDate('shifts.created_at', now())
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
            ->whereDate('shifts.created_at', now())
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
            ->whereDate('shifts.created_at', now())
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
