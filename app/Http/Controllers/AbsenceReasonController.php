<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsenceReasonController extends Controller
{
    public function index()
    {
        return \App\Http\Resources\AbsenceReasonResource::collection(\App\Models\AbsenceReason::all());
    }

    public function store(
        \App\Http\Requests\AbsenceReasonRequest $request
    ) {
        $absenceReason = $request->createAbsenceReason();
        return new \App\Http\Resources\AbsenceReasonResource($absenceReason);
    }

    public function show(\App\Models\AbsenceReason $absenceReason): \App\Http\Resources\AbsenceReasonResource
    {
        return new \App\Http\Resources\AbsenceReasonResource($absenceReason);
    }

    public function update(
        \App\Http\Requests\AbsenceReasonRequest $request,
        \App\Models\AbsenceReason $absenceReason
    ): \App\Http\Resources\AbsenceReasonResource {
        $absenceReason = $request->updateAbsenceReason($absenceReason);
        return new \App\Http\Resources\AbsenceReasonResource($absenceReason);
    }

    public function destroy(\App\Models\AbsenceReason $absenceReason): \Illuminate\Http\JsonResponse
    {
        $absenceReason->delete();
        return response()->json(null, 204);
    }
}
