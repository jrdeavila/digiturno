<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttentionProfileController extends Controller
{

    public function index()
    {
        $attentionProfiles = \App\Models\AttentionProfile::all();
        return \App\Http\Resources\AttentionProfileResource::collection($attentionProfiles);
    }


    public function store(\App\Http\Requests\AttentionProfileRequest $request)
    {
        $attentionProfile = \App\Models\AttentionProfile::create($request->all());
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function show(\App\Models\AttentionProfile $attentionProfile)
    {
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function update(\App\Http\Requests\AttentionProfileRequest $request, \App\Models\AttentionProfile $attentionProfile)
    {
        $attentionProfile->update($request->all());
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function destroy(\App\Models\AttentionProfile $attentionProfile)
    {
        $attentionProfile->delete();
        return response()->json(null, 204);
    }
}
