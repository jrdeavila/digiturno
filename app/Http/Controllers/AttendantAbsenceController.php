<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendantAbsenceController extends Controller
{
    public function index(\App\Models\Attendant $attendant)
    {
        return \App\Http\Resources\AttendantAbsenceResource::collection($attendant->absences);
    }

    public function store(
        \App\Http\Requests\AttendantAbsenceRequest $request,
        \App\Models\Attendant $attendant
    ) {
        $absence = $request->createAbsence($attendant);
        return new \App\Http\Resources\AttendantAbsenceResource($absence);
    }

    public function backToWork(
        \App\Models\Attendant $attendant,
    ) {
        $attendant->update([
            'status' => \App\Enums\AttendantStatus::Free
        ]);
        return response()->noContent();
    }
}
