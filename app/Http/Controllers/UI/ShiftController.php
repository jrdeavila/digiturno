<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\AttentionProfile;
use App\Models\Branch;
use App\Models\Qualification;
use App\Models\Room;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::query()
            ->when($request->get('branch_id'), function ($query) use ($request) {
                $query->whereHas('room', function ($query) use ($request) {
                    $query->where('branch_id', $request->get('branch_id'));
                });
            })
            ->when($request->get('room_id'), function ($query) use ($request) {
                $query->where('room_id', $request->get('room_id'));
            })
            ->when($request->get('attention_profile_id'), function ($query) use ($request) {
                $query->where('attention_profile_id', $request->get('attention_profile_id'));
            })
            ->when($request->get('state'), function ($query) use ($request) {
                $query->where('state', $request->get('state'));
            })
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->get('end_date'));
            })
            ->when($request->get('qualification'), function ($query) use ($request) {
                $query->whereHas('qualification', function ($query) use ($request) {
                    $query->where('qualification', $request->get('qualification'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $branches = Branch::all();
        $rooms = $request->get('branch_id') ? Room::where('branch_id', $request->get('branch_id'))->get() : Room::all();
        $attentionProfiles = $request->get('room_id') ? Room::find($request->get('room_id'))->attentionProfiles : AttentionProfile::all();

        return view('admin.shifts.index', compact('shifts', 'branches', 'rooms', 'attentionProfiles'));
    }

    public function show(Shift $shift)
    {
        return view('admin.shifts.show', compact('shift'));
    }

    public function destroy(Shift $shift)
    {
        try {
            $shift->forceDelete();
            return redirect()->route('admin.shifts.index')->with('success', 'El turno fue eliminado con exito');
        } catch (Exception $e) {
            return redirect()->route('admin.shifts.index')->with('error', 'El turno no pudo ser eliminado');
        }
    }
}
