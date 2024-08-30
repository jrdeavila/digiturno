<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttentionProfileController extends Controller
{

    public function adminIndex(Request $request)
    {
        $attentionProfiles = \Illuminate\Support\Facades\Cache::remember('attention_profiles', 60, function () {
            return \App\Models\AttentionProfile::latest()->get();
        });
        return \App\Http\Resources\AttentionProfileResource::collection($attentionProfiles);
    }

    public function adminStore(\App\Http\Requests\AttentionProfileRequest $request)
    {
        $attentionProfile =  $request->createAttentionProfile();
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function adminUpdate(\App\Http\Requests\AttentionProfileRequest $request, \App\Models\AttentionProfile $attentionProfile)
    {
        $request->updateAttentionProfile($attentionProfile);
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function adminDestroy(\App\Models\AttentionProfile $attentionProfile)
    {
        $attentionProfile->delete();
        return response()->json(null, 204);
    }


    public function index(Request $request)
    {
        $module = $request->module;
        $attentionProfiles = \Illuminate\Support\Facades\Cache::remember('attention_profiles', 60, function () use ($module) {
            return $module->room->attentionProfiles;
        });
        return \App\Http\Resources\AttentionProfileResource::collection($attentionProfiles);
    }


    public function store(\App\Http\Requests\AttentionProfileRequest $request)
    {
        $attentionProfile =  $request->createAttentionProfile();
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function show(\App\Models\AttentionProfile $attentionProfile)
    {
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function update(\App\Http\Requests\AttentionProfileRequest $request, \App\Models\AttentionProfile $attentionProfile)
    {
        $request->updateAttentionProfile($attentionProfile);
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function destroy(\App\Models\AttentionProfile $attentionProfile)
    {
        $attentionProfile->delete();
        return response()->json(null, 204);
    }
}
