<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendantController extends Controller
{
    public function index()
    {
        $attendants =  \Illuminate\Support\Facades\Cache::remember('attendants', 60, function () {
            return \App\Models\Attendant::latest()->get();
        });
        return \App\Http\Resources\AttendantResource::collection($attendants);
    }

    public function store(
        \App\Http\Requests\AttendantRequest $request,
    ) {
        $attendant = $request->createAttendant();
        return new \App\Http\Resources\AttendantResource($attendant);
    }

    public function show(\App\Models\Attendant $attendant)
    {
        $attendant = \Illuminate\Support\Facades\Cache::remember("attendant-{$attendant->id}", 60, function () use ($attendant) {
            return $attendant;
        });
        return new \App\Http\Resources\AttendantResource($attendant);
    }

    public function update(
        \App\Http\Requests\AttendantRequest $request,
        \App\Models\Attendant $attendant
    ) {
        $request->updateAttendant($attendant);
        return new \App\Http\Resources\AttendantResource($attendant);
    }

    public function destroy(\App\Models\Attendant $attendant)
    {
        $attendant->delete();
        return response()->json(null, 204);
    }
}
