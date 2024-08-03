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
}
