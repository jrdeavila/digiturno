<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Exception;
use Illuminate\Support\Facades\DB;

class RemoveAllDistractedShiftController extends Controller
{
    public function __invoke(Room $room)
    {
        try {
            DB::beginTransaction();
            $room->distractedShifts()->delete();
            DB::commit();
            return redirect()->route('attention.customer-reception.index')->with('success', 'Distraidos de la sala eliminados con exito');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('attention.customer-reception.index')->with('error', 'No se pudo eliminar el turno');
        }
    }
}
