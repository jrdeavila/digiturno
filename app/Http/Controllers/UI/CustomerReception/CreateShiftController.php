<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Client;
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
            'dni' => 'required|string|regex:/^[0-9]+$/|exists:clients,dni',
            'name' => 'required|string',
            'client_type_id' => 'required|exists:client_types,id',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $user = User::find(Auth::id());
        if (is_null($user)) {
            return redirect()->back()->with('error', 'El usuario no esta logueado');
        }




        try {
            DB::beginTransaction();
            $client = Client::find($request->get('client_id'));
            if (is_null($client)) {
                $client = new Client();
            }
            $client->name = $request->get('name');
            $client->dni = $request->get('dni');
            $client->client_type_id = $request->get('client_type_id');
            $client->save();
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo crear el turno');
        }
    }
}
