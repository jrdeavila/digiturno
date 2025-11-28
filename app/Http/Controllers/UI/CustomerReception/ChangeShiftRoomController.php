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

class ChangeShiftRoomController extends Controller
{
  public function __invoke(Shift $shift, Request $request)
  {
    $request->validate([
      'room_id' => 'required|exists:rooms,id'
    ]);

    try {
      DB::beginTransaction();
      $module = $this->searchModule($request->room_id, $shift->attention_profile_id);
      $shift->module_id = $module->id;
      $shift->room_id = $request->room_id;
      $shift->save();
      DB::commit();
      return redirect()->back()->with('success', 'Sala cambiada con exito');
    } catch (ModelNotFoundException $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo encontrar un modulo para la sala seleccionada');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo cambiar la sala: ' . $e->getMessage());
    }
  }


  private function searchModule(int $roomId, int $attentionProfileId): Module
  {
    $room = Room::find($roomId);
    $module = $room->modules()
      ->whereHas('attentionProfiles', function ($query) use ($attentionProfileId) {
        $query->where('attention_profiles.id', $attentionProfileId);
      })
      ->where('status', ModuleStatus::Online)
      ->withCount('pendingShifts')
      ->orderBy('pending_shifts_count', 'asc')
      ->firstOrFail();

    return $module;
  }
}
