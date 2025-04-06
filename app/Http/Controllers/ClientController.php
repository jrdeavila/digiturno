<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Agregar caché para la lista de clientes
        $clients = Cache::remember('clients.all', 3600, function () {
            return \App\Models\Client::latest()->get();
        });

        return \App\Http\Resources\ClientResource::collection($clients);
    }

    // Filtrar por dni o nombre y retornar la primera coincidencia
    public function find(Request $request)
    {
        // Crear una clave de caché basada en los parámetros de búsqueda
        $cacheKey = 'clients.find.' . ($request->dni ?? $request->name);

        $client = Cache::remember($cacheKey, 3600, function () use ($request) {
            $clients = \App\Models\Client::query();

            if ($request->has('dni')) {
                $clients->where('dni', $request->dni);
            }

            if ($request->has('name')) {
                $clients->whereRaw('LOWER(name) LIKE ?', ["%{$request->name}%"]);
            }

            return $clients->firstOrFail();
        });

        return new \App\Http\Resources\ClientResource($client);
    }

    public function store(\App\Http\Requests\ClientRequest $request)
    {
        $client = $request->createClient();

        // Limpiar la caché de la lista de clientes
        Cache::forget('clients.all');

        return new \App\Http\Resources\ClientResource($client);
    }

    public function show(\App\Models\Client $client)
    {
        return new \App\Http\Resources\ClientResource($client);
    }

    public function update(\App\Http\Requests\ClientRequest $request, \App\Models\Client $client)
    {
        $request->updateClient($client);

        // Limpiar la caché de la lista de clientes
        Cache::forget('clients.all');

        return new \App\Http\Resources\ClientResource($client);
    }

    public function destroy(\App\Models\Client $client)
    {
        $client->delete();

        // Limpiar la caché de la lista de clientes
        Cache::forget('clients.all');

        return response()->noContent();
    }

    public function restore(int $clientId)
    {
        $client = \App\Models\Client::withTrashed()->findOrFail($clientId);
        $client->restore();

        // Limpiar la caché de la lista de clientes
        Cache::forget('clients.all');

        return response()->noContent();
    }

    public function forceDelete(int $clientId)
    {
        $client = \App\Models\Client::withTrashed()->findOrFail($clientId);
        $client->forceDelete();

        Cache::forget('clients.all');

        return response()->noContent();
    }
}
