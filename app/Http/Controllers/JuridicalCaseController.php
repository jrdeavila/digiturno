<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JuridicalCaseController extends Controller
{
    public function index(\App\Models\Attendant $attendant)
    {
        $cases = $attendant->juridicalCases;
        return new \App\Http\Resources\JuridicalCaseCollection($cases);
    }

    public function store(\App\Models\Attendant $attendant, \App\Http\Requests\JuridicalCaseRequest $request)
    {
        $case = $request->createJuridicalCase($attendant);
        return new \App\Http\Resources\JuridicalCaseResource($case);
    }

    public function show(\App\Models\Attendant $attendant, \App\Models\JuridicalCase $juridicalCase)
    {
        throw_unless($attendant->id == $juridicalCase->attendant_id, \App\Exceptions\JuridicalCaseAttendantUnauthorizedException::class);
        return new \App\Http\Resources\JuridicalCaseResource($juridicalCase);
    }

    public function update(\App\Models\Attendant $attendant, \App\Models\JuridicalCase $juridicalCase, \App\Http\Requests\JuridicalCaseRequest $request)
    {
        throw_unless($attendant->id == $juridicalCase->attendant_id, \App\Exceptions\JuridicalCaseAttendantUnauthorizedException::class);
        $juridicalCase = $request->updateJuridicalCase($juridicalCase);
        return new \App\Http\Resources\JuridicalCaseResource($juridicalCase);
    }

    public function destroy(\App\Models\Attendant $attendant, \App\Models\JuridicalCase $juridicalCase)
    {
        throw_unless($attendant->id == $juridicalCase->attendant_id, \App\Exceptions\JuridicalCaseAttendantUnauthorizedException::class);
        $juridicalCase->delete();
        return response()->noContent();
    }

    public function addObservation(
        \App\Models\Attendant $attendant,
        \App\Models\JuridicalCase $juridicalCase,
        \App\Http\Requests\JuridicalCaseObservationRequest $request
    ) {
        $observation = $request->createObservation($juridicalCase);
        $observation->load('attendant')->load("juridicalCase");
        return new \App\Http\Resources\JuridicalCaseObservationResource($observation);
    }

    public function destroyObservation(
        \App\Models\Attendant $attendant,
        \App\Models\JuridicalCaseObservation $observation
    ) {
        $observation->delete();
        return response()->noContent();
    }
}
