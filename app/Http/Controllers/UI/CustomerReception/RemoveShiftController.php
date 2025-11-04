<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;

class RemoveShiftController extends Controller
{
    public function __invoke(Request $request, Shift $shift)
    {
        try {
            $shift->delete();
            return redirect()->route('attention.customer-reception.index')->with('success', 'Turno eliminado con exito');
        } catch (Exception $e) {
            return redirect()->route('attention.customer-reception.index')->with('error', 'No se pudo eliminar el turno');
        }
    }
}
