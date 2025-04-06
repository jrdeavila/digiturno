<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = \App\Models\Room::all();
        return \App\Http\Resources\RoomResource::collection($rooms);
    }

    public function store(\App\Http\Requests\RoomRequest $request)
    {
        $room = \App\Models\Room::create($request->all());
        return new \App\Http\Resources\RoomResource($room);
    }

    public function show(\App\Models\Room $room)
    {
        return new \App\Http\Resources\RoomResource($room);
    }

    public function update(\App\Http\Requests\RoomRequest $request, \App\Models\Room $room)
    {
        $room->update($request->all());
        return new \App\Http\Resources\RoomResource($room);
    }

    public function destroy(\App\Models\Room $room)
    {
        $room->delete();
        return response()->json(null, 204);
    }
}
