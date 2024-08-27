<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleTypeController extends Controller
{
    public function index()
    {
        return \App\Http\Resources\ModuleTypeResource::collection(\App\Models\ModuleType::all());
    }

    public function store(\App\Http\Requests\ModuleTypeRequest $request)
    {
        $moduleType = $request->createModuleType();
        return new \App\Http\Resources\ModuleTypeResource($moduleType);
    }

    public function show(\App\Models\ModuleType $moduleType)
    {
        return new \App\Http\Resources\ModuleTypeResource($moduleType);
    }

    public function update(\App\Http\Requests\ModuleTypeRequest $request, \App\Models\ModuleType $moduleType)
    {
        $moduleType = $request->updateModuleType($moduleType);
        return new \App\Http\Resources\ModuleTypeResource($moduleType);
    }

    public function destroy(\App\Models\ModuleType $moduleType)
    {
        $moduleType->delete();
        return response()->json(null, 204);
    }
}
