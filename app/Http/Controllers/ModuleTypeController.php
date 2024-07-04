<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleTypeController extends Controller
{
    public function index()
    {
        $moduleTypes =  \Illuminate\Support\Facades\Cache::remember('moduleTypes', 60, function () {
            return \App\Models\ModuleType::all();
        });
        return \App\Http\Resources\ModuleTypeResource::collection($moduleTypes);
    }

    public function show(\App\Models\ModuleType $moduleType)
    {
        $moduleType = \Illuminate\Support\Facades\Cache::remember("moduleType-{$moduleType->id}", 60, function () use ($moduleType) {
            return $moduleType;
        });
        return new \App\Http\Resources\ModuleTypeResource($moduleType);
    }
}
