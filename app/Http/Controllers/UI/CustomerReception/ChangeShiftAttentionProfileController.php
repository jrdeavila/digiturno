<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Enums\ModuleStatus;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Room;
use App\Models\Shift;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeShiftAttentionProfileController extends Controller
{
  public function __invoke(Shift $shift, Request $request)
  {
    $request->validate([
      'attention_profile_id' => 'required|exists:attention_profiles,id',
    ]);

    try {
      DB::beginTransaction();
      $module = $this->searchModule($shift->room_id, $request->attention_profile_id);
      $shift->attention_profile_id = $request->attention_profile_id;
      $shift->module_id = $module->id;
      $shift->save();
      DB::commit();
      return redirect()->back()->with('success', 'Perfil de atencion cambiado con exito');
    } catch (ModelNotFoundException $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo encontrar un modulo para el perfil de atencion seleccionado');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo cambiar el perfil de atencion: ' . $e->getMessage());
    }
  }


  private function searchModule(int $roomId, int $attentionProfileId): Module
  {
    $room = Room::find($roomId);
    $module = $room->modules()
      ->whereHas('attentionProfiles', function ($query)  use ($attentionProfileId) {
        $query->where('attention_profiles.id', $attentionProfileId);
      })
      ->where('status', ModuleStatus::Online)
      ->withCount('pendingShifts')
      ->orderBy('pending_shifts_count', 'asc')
      ->firstOrFail();

    return $module;
  }
}
