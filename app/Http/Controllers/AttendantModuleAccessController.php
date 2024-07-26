<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendantModuleAccessController extends Controller
{
    public function index()
    {
        $accesses = \App\Models\ModuleAttendantAccess::all();
        return \App\Http\Resources\ModuleAttendantAccessResource::collection($accesses);
    }
}
