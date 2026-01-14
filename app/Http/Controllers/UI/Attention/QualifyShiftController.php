<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\QualificationOption;
use App\Http\Controllers\Controller;
use App\Models\Qualification;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualifyShiftController extends Controller
{
  public function __invoke(Shift $shift, Request $request)
  {
    $request->validate([
      'qualification' => 'required|in:' . implode(',', array_map(function ($option) {
        return $option->value;
      }, QualificationOption::cases()),),
    ]);
    try {
      DB::beginTransaction();
      Qualification::create([
        'shift_id' => $shift->id,
        'qualification' => $request->qualification
      ]);
      DB::commit();
      return redirect()->back()->with('success', 'Turno calificado con exito');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Error al calificar el turno');
    }
  }
}
