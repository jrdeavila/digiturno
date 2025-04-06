<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(\App\Http\Requests\Auth\AttendantLoginRequest $request)
    {
        $token = $request->login();
        return response()->json(['token' => $token]);
    }

    public function profile()
    {
        $attendant = auth('attendant')->user();
        if ($attendant->status === \App\Enums\AttendantStatus::Offline) {
            $attendant->status = \App\Enums\AttendantStatus::Free;
        }
        return new \App\Http\Resources\AttendantResource($attendant);
    }

    public function logout()
    {
        $module = request()->module;
        \App\Jobs\AttendantLogout::dispatch(auth('attendant')->user(), $module);
        auth('attendant')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $token = auth('attendant')->refresh();
        return response()->json(['token' => $token]);
    }

    public function offline()
    {
        $attendant = auth('attendant')->user();
        $module = request()->module;
        $module->status = \App\Enums\ModuleStatus::Offline;
        $module->save();
        $attendant->status = \App\Enums\AttendantStatus::Offline;
        $attendant->save();
        return response()->json(['message' => 'Successfully offline']);
    }
}
