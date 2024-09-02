<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = \Illuminate\Support\Facades\Cache::remember('clients', 512, function () {
            return \App\Models\Client::latest()->get();
        });
        return \App\Http\Resources\ClientResource::collection($clients);
    }


    public function store(
        \App\Http\Requests\ClientRequest $request,
    ) {
        $client = $request->createClient();
        return new \App\Http\Resources\ClientResource($client);
    }


    public function show(\App\Models\Client $client)
    {
        return new \App\Http\Resources\ClientResource($client);
    }

    public function update(
        \App\Http\Requests\ClientRequest $request,
        \App\Models\Client $client
    ) {
        $request->updateClient($client);
        return new \App\Http\Resources\ClientResource($client);
    }

    public function destroy(\App\Models\Client $client)
    {
        $client->delete();
        return response()->noContent();
    }

    public function restore(int $clientId)
    {
        $client = \App\Models\Client::withTrashed()->findOrFail($clientId);
        $client->restore();
        return response()->noContent();
    }

    public function forceDelete(int $clientId)
    {
        $client = \App\Models\Client::withTrashed()->findOrFail($clientId);
        $client->forceDelete();
        return response()->noContent();
    }
}
