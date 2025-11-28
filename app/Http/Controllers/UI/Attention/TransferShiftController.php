<?php

namespace App\Http\Controllers\UI\Attention;

use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Jobs\ShiftTransferred;
use App\Models\AttentionProfile;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferShiftController extends Controller
{
  public function __invoke(Shift $shift, Request $request)
  {
    $request->validate([
      'services' => 'required|array|min:1',
      'services.*' => 'required|exists:services,id',
      'attention_profile_id' => 'required|exists:attention_profiles,id',
    ]);

    try {
      DB::beginTransaction();
      $shift->state = ShiftState::Transferred;
      $shift->save();
      $shift->services()->sync($request->services);
      DB::commit();
      $attentionProfile = AttentionProfile::find($request->attention_profile_id);
      $job = new ShiftTransferred($shift, $attentionProfile);
      dispatch($job);
      return redirect()->back();
    } catch (Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'No se pudo transferir el turno: ' . $e->getMessage());
    }
  }
}
