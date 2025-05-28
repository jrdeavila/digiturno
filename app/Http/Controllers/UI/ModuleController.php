<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\AttentionProfile;
use App\Models\Branch;
use App\Models\ClientType;
use App\Models\Employee;
use App\Models\Module;
use App\Models\ModuleType;
use App\Models\Room;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    public function index(Request $request)
    {

        $modules = Module::query()
            ->when($request->get('branch_id'), function ($query) use ($request) {
                $branch = Branch::find($request->get('branch_id'));
                $roomIds = $branch->rooms->pluck('id')->toArray();
                $query->whereIn('room_id', $roomIds);
            })
            ->when($request->get('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            })
            ->when($request->get('client_type_id'), function ($query) use ($request) {
                $query->where('client_type_id', $request->get('client_type_id'));
            })
            ->when($request->get('module_type_id'), function ($query) use ($request) {
                $query->where('module_type_id', $request->get('module_type_id'));
            })
            ->when($request->get('attention_profile_id'), function ($query) use ($request) {
                $query->whereHas('attentionProfiles', function ($query) use ($request) {
                    $query->where('attention_profiles.id', $request->get('attention_profile_id'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        $branches = Branch::all();
        $rooms = Room::query()
            ->when($request->get('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->get('branch_id'));
            })
            ->get();
        $attentionProfiles = AttentionProfile::query()
            ->when($request->get('branch_id'), function ($query) use ($request) {
                $query->whereHas('rooms', function ($query) use ($request) {
                    $query->where('branch_id', $request->get('branch_id'));
                });
            })
            ->get();
        $clientTypes = ClientType::query()->get();
        $moduleTypes = ModuleType::query()->get();
        return view('modules.index', compact('modules', 'branches', 'rooms', 'attentionProfiles', 'clientTypes', 'moduleTypes'));
    }

    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    public function create(?Room $room, Request $request)
    {
        $dni = $request->get('dni');
        $validator = Validator::make([
            'dni' => $dni,
        ], [
            'dni' => 'nullable|numeric|exists:' . Employee::class . ',noDocumento',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userSelected = null;
        if ($dni) {
            $userSelected = User::whereHas('employee', function ($query) use ($dni) {
                $query->where('noDocumento', $dni);
            })->first();
        }
        $branches = Branch::all();
        $clientTypes = ClientType::all();
        $moduleTypes = ModuleType::all();
        return view('modules.create', compact('room', 'branches',   'clientTypes', 'moduleTypes', 'userSelected'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'room_id' => 'required|exists:rooms,id',
            'client_type_id' => 'nullable|exists:client_types,id',
            'module_type_id' => 'required|exists:module_types,id',
            'user_id' => 'nullable|exists:' . User::class . ',id',
            'attention_profiles' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            $module =  Module::create($request->all());
            if ($request->get('user_id')) {
                $module->responsable()->associate($request->get('user_id'));
            }
            if ($request->get('attention_profiles')) {
                $module->attentionProfiles()->sync($request->attention_profiles);
            }
            $module->save();
            DB::commit();
            return redirect()->route('modules.index')->with('success', 'El modulo fue creado con exito');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El modulo no pudo ser creado: ' . $e->getMessage());
        }
    }

    public function edit(Module $module, Request $request)
    {
        $employee = $module->responsable ? $module->responsable->employee : null;
        $dni = $request->get('dni', $employee ? $employee->document_number : null);
        $validator = Validator::make([
            'dni' => $dni,
        ], [
            'dni' => 'nullable|numeric|exists:' . Employee::class . ',noDocumento',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userSelected = null;
        if ($dni) {
            $userSelected = User::whereHas('employee', function ($query) use ($dni) {
                $query->where('noDocumento', $dni);
            })->first();
        }
        $clientTypes = ClientType::all();
        $moduleTypes = ModuleType::all();
        $attentionProfiles = AttentionProfile::all();
        return view('modules.edit', compact('module', 'clientTypes', 'moduleTypes', 'userSelected', 'attentionProfiles'));
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => 'required|string',
            'client_type_id' => 'required|exists:client_types,id',
            'module_type_id' => 'required|exists:module_types,id',
            'attention_profile_id' => 'nullable|exists:attention_profiles,id',
        ]);

        try {
            DB::beginTransaction();
            $module->name = $request->get('name', $module->name);
            $module->client_type_id = $request->get('client_type_id', $module->client_type_id);
            $module->module_type_id = $request->get('module_type_id', $module->module_type_id);
            $module->responsable()->dissociate();

            if ($request->get('attention_profile_id')) {
                $module->attentionProfiles()->sync([$request->get('attention_profile_id')]);
            }
            if ($request->get('user_id')) {
                $module->responsable()->associate($request->get('user_id'));
            }
            $module->save();
            DB::commit();
            return redirect()->route('modules.index')->with('success', 'El modulo fue actualizado con exito');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El modulo no pudo ser actualizado: ' . $e->getMessage());
        }
    }

    public function destroy(Module $module)
    {
        try {
            $module->forceDelete();
            return redirect()->route('modules.index')->with('success', 'El modulo fue eliminado con exito');
        } catch (Exception $e) {
            return redirect()->route('modules.index')->with('error', 'El modulo no pudo ser eliminado');
        }
    }
}
