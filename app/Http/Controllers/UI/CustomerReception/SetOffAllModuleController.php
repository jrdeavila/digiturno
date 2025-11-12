<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Enums\ModuleStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Exception;
use Illuminate\Support\Facades\DB;

class SetOffAllModuleController extends Controller
{
  public function __invoke(Room $room)
  {
    try {

      DB::beginTransaction();
      $modules = $room->modules()->get();
      foreach ($modules as $module) {
        $module->status = ModuleStatus::Offline;
        $module->save();
      }
      DB::commit();
      return redirect()->back()->with('success', 'Modulos apagados con exito');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo apagar los modulos');
    }
  }
}
