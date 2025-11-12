<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;

class SendShiftToDistractedController extends Controller
{
    public function __invoke(Request $request, Shift $shift)
    {
        try {
            $shift->state = ShiftState::Distracted;
            $shift->save();
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'No se pudo enviar el turno a distraidos: ' . $e->getMessage());
        }
    }
}
