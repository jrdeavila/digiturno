<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->get('name'), function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->get('name') . '%');
            })
            ->when($request->get('dni'), function ($query) use ($request) {
                $query->orWhere('dni', 'like', '%' . $request->get('dni') . '%');
            })
            ->when($request->get('client_type_id'), function ($query) use ($request) {
                $query->where('client_type_id', $request->get('client_type_id'));
            })

            ->latest()->paginate(5);
        $clientTypes = ClientType::latest()->get();
        return view('admin.clients.index', compact('clients', 'clientTypes'));
    }

    public function create()
    {
        $clientTypes = ClientType::all();
        return view('admin.clients.create', compact('clientTypes'));
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'dni' => 'required|string|unique:clients,dni',
            'client_type_id' => 'required|exists:client_types,id',
        ]);

        try {
            DB::beginTransaction();
            Client::create($request->all());
            DB::commit();
            return redirect()->route('admin.clients.index')->with('success', 'El cliente fue creado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El cliente no pudo ser creado');
        }
    }

    public function edit(Client $client)
    {
        $clientTypes = ClientType::all();
        return view('admin.clients.edit', compact('client', 'clientTypes'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string',
            'dni' => 'required|string|unique:clients,dni,' . $client->id,
            'client_type_id' => 'required|exists:client_types,id',
        ]);

        try {
            DB::beginTransaction();
            $client->update($request->all());
            DB::commit();
            return redirect()->route('admin.clients.index')->with('success', 'El cliente fue actualizado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'El cliente no pudo ser actualizado');
        }
    }

    public function destroy(Client $client)
    {
        try {
            $client->forceDelete();
            return redirect()->route('admin.clients.index')->with('success', 'El cliente fue eliminado con exito');
        } catch (Exception $e) {
            return redirect()->route('admin.clients.index')->with('error', 'El cliente no pudo ser eliminado');
        }
    }
}
