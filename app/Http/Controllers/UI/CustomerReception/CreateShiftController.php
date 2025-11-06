<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Enums\ModuleStatus;
use App\Enums\ShiftState;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Module;
use App\Models\Room;
use App\Models\Shift;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateShiftController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'attention_profile_id' => 'required|exists:attention_profiles,id',
            'dni' => 'required|string|regex:/^[0-9]+$/',
            'name' => 'required|string',
            'client_type_id' => 'required|exists:client_types,id',
            'client_id' => 'nullable|exists:clients,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = User::find(Auth::id());
        if (is_null($user)) {
            return redirect()->back()->with('error', 'El usuario no esta logueado');
        }

        try {
            DB::beginTransaction();
            $client = $this->refreshClientInfo($request);

            $this->createShift($client, $request, $user);
            DB::commit();
            return redirect()->route('attention.customer-reception.index')->with('success', 'El turno fue creado con exito');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo crear el turno: ' . $exception->getMessage());
        }
    }

    private function createShift(Client $client, Request $request, User $user)
    {
        $exists = Shift::where('client_id', $client->id)
            ->whereIn('state', [
                ShiftState::Pending,
                ShiftState::InProgress,
                ShiftState::PendingTransferred,
                ShiftState::Transferred,
                ShiftState::Distracted,
            ])->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'El cliente ya tiene un turno pendiente');
        }
        $module = $this->searchModule($request);
        Shift::create([
            'attention_profile_id' => $request->get('attention_profile_id'),
            'client_id' => $client->id,
            'room_id' => $request->get('room_id'),
            'module_id' => $module->id
        ]);
    }

    private function searchModule(Request $request): Module
    {
        $room = Room::find($request->get('room_id'));
        $module = $room->modules()
            ->where('client_type_id', $request->get('client_type_id'))
            ->whereHas('attentionProfiles', function ($query) use ($request) {
                $query->where('attention_profiles.id', $request->get('attention_profile_id'));
            })
            ->where('status', ModuleStatus::Online)
            // Ordenar por cantidad de turnos con estado pendientes
            ->withCount('pendingShifts')
            ->orderBy('pending_shifts_count', 'asc')
            ->firstOrFail();
        // Distribuir la carga de los turnos en los módulos

        return $module;
    }

    private function refreshClientInfo(Request $request): Client

    {
        $client = Client::where('dni', $request->get('dni'))->first();
        if (!$client) {
            if ($request->get('client_id')) {
                $client = Client::find($request->get('client_id'));
            }
        }

        if (is_null($client)) {
            $client = new Client();
        }
        $client->name = $request->get('name');
        $client->dni = $request->get('dni');
        $client->client_type_id = $request->get('client_type_id');
        $client->save();

        return $client;
    }
}
