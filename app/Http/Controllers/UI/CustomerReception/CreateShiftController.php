<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Enums\ModuleStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Module;
use App\Models\MregEstInscrito;
use App\Models\Room;
use App\Models\Shift;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            'name' => 'nullable|string',
            'client_type_id' => 'nullable|exists:client_types,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = User::query()->find(Auth::id());
        if (null === $user) {
            return redirect()->back()->with('error', 'El usuario no esta logueado');
        }

        try {
            DB::beginTransaction();
            $client = $this->refreshClientInfo($request);
            if (null === $client) {
                $exception = new ModelNotFoundException();
                $exception->setModel(Client::class);
                throw $exception;
            }
            $this->createShift($client, $request, $user);
            DB::commit();
            return redirect()->back()->with('success', 'El turno fue creado con exito');
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            if ($exception->getModel() === Module::class) {
                return redirect()->back()->with('error', 'No hay modulos disponibles')->withInput($request->all());
            }
            if ($exception->getModel() === Client::class) {
                return redirect()->back()->with('error', 'No se encontro el cliente')->with('searched', true)->withInput($request->all());
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo crear el turno: ' . $exception->getMessage())->withInput($request->all());
        }
    }

    private function createShift(Client $client, Request $request, User $user)
    {
        $exists = Shift::query()->where('client_id', $client->id)
            ->current()->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'El cliente ya tiene un turno pendiente');
        }
        $module = $this->searchModule($request);
        Shift::query()->create([
            'attention_profile_id' => $request->get('attention_profile_id'),
            'client_id' => $client->id,
            'room_id' => $request->get('room_id'),
            'module_id' => $module->id,
        ]);
    }

    private function searchModule(Request $request): Module
    {
        $room = Room::query()->find($request->get('room_id'));
        $module = $room->modules()
            ->whereHas('attentionProfiles', function ($query) use ($request) {
                $query->where('attention_profiles.id', $request->get('attention_profile_id'));
            })
            ->where('status', ModuleStatus::Online)
            ->withCount('pendingShifts')
            ->orderBy('pending_shifts_count', 'asc')
            ->firstOrFail();

        return $module;
    }


    private function refreshClientInfo(Request $request): ?Client
    {
        $client = Client::query()->where('dni', $request->get('dni'))->first();

        if (!$client) { // Si no se encuentra el cliente por dni buscamos en los registros del S.I.I
            $mreg = MregEstInscrito::query()->where('is_member', 1)->where('is_member_since', now()->year)->where('id_number', $request->get('dni'))->first();
            if ($mreg) {
                $client = Client::query()->create([
                    'name' => $mreg->name,
                    'dni' => $mreg->id_number,
                    'client_type_id' => 3, // Afiliado
                ]);
            }
        }

        if (!$request->get('name') && !$request->get('client_type_id') && !$client) { // Si no se envio el nombre ni el tipo de cliente y no se encontro el cliente por dni
            return null;
        }

        if (!$client) { // Si aun no se encontro el cliente entonces creamos uno
            $client = new Client();
        }

        $client->name = $request->get('name', $client?->name);
        $client->dni = $request->get('dni', $client?->dni);
        $client->client_type_id = $request->get('client_type_id', $client?->client_type_id);
        $client->save();

        return $client;
    }
}
