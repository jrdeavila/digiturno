<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(\App\Http\Requests\Auth\Admin\LoginRequest $request)
    {
        $token = $request->login();
        return response()->json(['token' => $token]);
    }

    public function profile()
    {
        $admin = auth('admin')->user();
        return new \App\Http\Resources\AdminResource($admin);
    }

    public function logout()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $token = auth('admin')->refresh();
        return response()->json(['token' => $token]);
    }
}
