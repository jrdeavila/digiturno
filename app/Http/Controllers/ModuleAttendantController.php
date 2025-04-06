<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleAttendantController extends Controller
{
    public function index(\App\Models\Module $module)
    {
        $attendants = \Illuminate\Support\Facades\Cache::remember("module-{$module->id}-attendants", 60, function () use ($module) {
            return $module->attendants;
        });
        return \App\Http\Resources\AttendantResource::collection($attendants);
    }
}
