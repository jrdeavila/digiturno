<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\AttentionProfile;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionProfileServiceController extends Controller
{
    public function edit(AttentionProfile $attentionProfile, Request $request)
    {
        $services = Service::whereNull('service_id')->orderBy('name')->get();

        return view('attention_profiles.services.edit', compact('attentionProfile', 'services'));
    }

    public function update(AttentionProfile $attentionProfile, Request $request)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*' => 'required|exists:services,id'
        ]);
        try {
            DB::beginTransaction();
            $attentionProfile->services()->sync($request->services);
            DB::commit();
            return redirect()->back()->with('success', 'Cambios guardados');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo guardar los cambios');
        }
    }
}
