<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeShiftModuleController extends Controller
{
  public function __invoke(Shift $shift, Request $request)
  {
    $request->validate([
      'module_id' => 'required|exists:modules,id'
    ]);

    try {
      DB::beginTransaction();
      $shift->module_id = $request->module_id;
      $shift->save();
      DB::commit();
      return redirect()->back()->with('success', 'Modulo cambiado con exito');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo cambiar el modulo: ' . $e->getMessage());
    }
  }
}
