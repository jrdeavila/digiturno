<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinishShiftController extends Controller
{
    public function __invoke(Shift $shift, Request $request)

    {
        $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'required|exists:services,id',
        ]);
        try {
            DB::beginTransaction();
            $shift->state = ShiftState::Completed;
            $shift->save();
            $shift->services()->sync($request->services);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('attention.index')->with('error', 'No se pudo finalizar el turno: ' . $e->getMessage());
        }
    }
}
