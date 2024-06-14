<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = \App\Models\Shift::latest()->get();
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

    public function update(\App\Http\Requests\ShiftRequest $request, \App\Models\Shift $shift)
    {
        $shift = $request->updateShift($shift);
        return new \App\Http\Resources\ShiftResource($shift);
    }

    public function destroy(\App\Models\Shift $shift)
    {
        $shift->delete();
        return response()->json(null, 204);
    }
}
