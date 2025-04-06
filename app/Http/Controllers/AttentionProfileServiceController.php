<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttentionProfileServiceController extends Controller
{
    /** 
     *  Get all services of an attention profile
     */

    public function index(\App\Models\AttentionProfile $attentionProfile)
    {
        $services = \Illuminate\Support\Facades\Cache::remember("attention_profile.{$attentionProfile->id}.services", 60, function () use ($attentionProfile) {
            return $attentionProfile->services()->latest()->get();
        });
        return \App\Http\Resources\ServiceResource::collection($services);
    }

    /**
     *  Store a new service in an attention profile
     */

    public function store(
        \App\Http\Requests\AttentionProfileServiceRequest $request,
        \App\Models\AttentionProfile $attentionProfile
    ) {
        $service = \App\Models\Service::findOrFail($request->service_id);
        $attentionProfile->services()->attach($service);
        return new \App\Http\Resources\ServiceResource($service);
    }

    /**
     * Delete a service from an attention profile
     */

    public function destroy(
        \App\Models\AttentionProfile $attentionProfile,
        \App\Models\Service $service
    ) {
        $attentionProfile->services()->detach($service);
        return response()->json(null, 204);
    }
}
