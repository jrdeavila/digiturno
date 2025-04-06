<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientTypeController extends Controller
{
    public function index()
    {
        $clientTypes = \App\Models\ClientType::latest()->get();
        return \App\Http\Resources\ClientTypeResource::collection($clientTypes);
    }

    public function store(\App\Http\Requests\ClientTypeRequest $request)
    {
        $clientType = \App\Models\ClientType::create($request->all());
        return new \App\Http\Resources\ClientTypeResource($clientType);
    }

    public function show(\App\Models\ClientType $clientType)
    {
        return new \App\Http\Resources\ClientTypeResource($clientType);
    }

    public function update(\App\Http\Requests\ClientTypeRequest $request, \App\Models\ClientType $clientType)
    {
        $clientType->update($request->all());
        return new \App\Http\Resources\ClientTypeResource($clientType);
    }

    public function destroy(\App\Models\ClientType $clientType)
    {
        $clientType->delete();
        return response()->json(null, 204);
    }
}
