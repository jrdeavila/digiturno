<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Room;
use Exception;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::latest()->paginate(5);
        return view('rooms.index', compact('rooms'));
    }

    public function create(?Branch $branch)
    {
        $branches = Branch::all();
        return view('rooms.create', compact('branch', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:rooms,name',
            'branch_id' => 'required|exists:branches,id',
        ]);
        try {
            Room::create($request->all());
            return redirect()->route('rooms.index')->with('success', 'La sala fue creada con exito');
        } catch (Exception $e) {
            return redirect()->route('rooms.index')->with('error', 'La sala no pudo ser creada');
        }
    }

    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $branches = Branch::all();
        return view('rooms.edit', compact('room', 'branches'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|unique:rooms,name,' . $room->id,
            'branch_id' => 'required|exists:branches,id',
        ]);
        try {
            $room->update($request->all());
            return redirect()->route('rooms.index')->with('success', 'La sala fue actualizada con exito');
        } catch (Exception $e) {
            return redirect()->route('rooms.index')->with('error', 'La sala no pudo ser actualizada');
        }
    }

    public function destroy(Room $room)
    {
        try {
            $room->delete();
            return redirect()->route('rooms.index')->with('success', 'La sala fue eliminada con exito');
        } catch (Exception $e) {
            return redirect()->route('rooms.index')->with('error', 'La sala no pudo ser eliminada');
        }
    }
}
