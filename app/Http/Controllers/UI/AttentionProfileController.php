<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\AttentionProfile;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionProfileController extends Controller
{
    public function index()
    {
        $attentionProfiles = AttentionProfile::latest()->paginate(5);
        return view('attention_profiles.index', compact('attentionProfiles'));
    }

    public function show(AttentionProfile $attentionProfile)
    {
        return view('attention_profiles.show', compact('attentionProfile'));
    }

    public function create()
    {
        $services = Service::all();
        return view('attention_profiles.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ]);
        try {
            DB::beginTransaction();
            $attentionProfile = AttentionProfile::create($request->all());
            $attentionProfile->services()->sync($request->services);
            DB::commit();
            return redirect()->route('attention-profiles.index')->with('success', 'Perfil de Atencion creado con exito.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El perfil de atencion no pudo ser creado.');
        }
    }

    public function edit(AttentionProfile $attentionProfile)
    {
        $services = Service::all();
        return view('attention_profiles.edit', compact('attentionProfile', 'services'));
    }

    public function update(Request $request, AttentionProfile $attentionProfile)
    {
        $request->validate([
            'name' => 'required',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ]);
        try {
            DB::beginTransaction();
            $attentionProfile->update($request->all());
            $attentionProfile->services()->sync($request->services);
            DB::commit();
            return redirect()->route('attention-profiles.index')->with('success', 'Perfil de Atencion actualizado con exito.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El perfil de atencion no pudo ser actualizado.');
        }
    }

    public function destroy(AttentionProfile $attentionProfile)
    {
        try {
            $attentionProfile->forceDelete();
            return redirect()->route('attention-profiles.index')->with('success', 'Perfil de Atencion eliminado con exito.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'El perfil de atencion no pudo ser eliminado.');
        }
    }
}
