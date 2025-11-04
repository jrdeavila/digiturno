<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToUpShiftController extends Controller
{
    public function __invoke(Request $request, Shift $shift)
    {
        try {
            DB::beginTransaction();
            $shift->state = \App\Enums\ShiftState::Pending;
            $shift->save();
            DB::commit();
            return redirect()->route('attention.customer-reception.index')->with('success', 'Turno subido con exito');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo subir el turno: ' . $e->getMessage());
        }
    }
}
