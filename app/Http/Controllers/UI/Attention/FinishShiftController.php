<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;

class FinishShiftController extends Controller
{
    public function __invoke(Shift $shift)
    {
        try {
            $shift->state = ShiftState::Completed;
            $shift->save();
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->route('attention.index')->with('error', 'No se pudo finalizar el turno: ' . $e->getMessage());
        }
    }
}
