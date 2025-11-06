<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;

class CallShiftClientController extends Controller
{
    public function __invoke(Request $request, Shift $shift)
    {
        try {
            $shift->state = ShiftState::Called;
            $shift->save();
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'No se pudo llamar al cliente: ' . $e->getMessage());
        }
    }
}
