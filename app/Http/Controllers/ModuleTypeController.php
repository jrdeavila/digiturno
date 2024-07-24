<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleTypeController extends Controller
{
    public function index()
    {
        return \App\Http\Resources\ModuleTypeResource::collection(\App\Models\ModuleType::all());
    }
}
