<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendantModuleAccessController extends Controller
{
    public function index()
    {
        $accesses = \App\Models\ModuleAttendantAccess::whereDate('created_at', now()->toDateString())
            ->latest()->get();
        return \App\Http\Resources\ModuleAttendantAccessResource::collection($accesses);
    }
}
