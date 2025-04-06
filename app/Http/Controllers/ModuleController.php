<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules =  \Illuminate\Support\Facades\Cache::remember('modules', 3600, function () {
            return \App\Models\Module::latest()->get();
        });
        return \App\Http\Resources\ModuleResource::collection($modules);
    }

    public function store(
        \App\Http\Requests\ModuleRequest $request,
    ) {
        $module = $request->createModule();
        return new \App\Http\Resources\ModuleResource($module);
    }

    public function show(\App\Models\Module $module)
    {
        $module = \Illuminate\Support\Facades\Cache::remember("module-{$module->id}", 4600, function () use ($module) {
            return $module;
        });
        return new \App\Http\Resources\ModuleResource($module);
    }

    public function update(
        \App\Http\Requests\ModuleRequest $request,
        \App\Models\Module $module
    ) {
        $request->updateModule($module);
        return new \App\Http\Resources\ModuleResource($module);
    }

    public function destroy(\App\Models\Module $module)
    {
        $module->delete();
        return response()->json(null, 204);
    }

    public function mySelf(Request $request)
    {
        $module = $request->module;
        return new \App\Http\Resources\ModuleResource($module);
    }
}
