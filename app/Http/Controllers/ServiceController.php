<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = \Illuminate\Support\Facades\Cache::remember('services', 60, function () {
            return \App\Models\Service::latest()->get();
        });
        return \App\Http\Resources\ServiceResource::collection($services);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(
        \App\Http\Requests\ServiceRequest $request,
    ) {
        $service = $request->createService();
        return new \App\Http\Resources\ServiceResource($service);
    }


    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Service $service)
    {
        return new \App\Http\Resources\ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(
        \App\Http\Requests\ServiceRequest $request,
        \App\Models\Service $service
    ) {
        $request->updateService($service);
        return new \App\Http\Resources\ServiceResource($service);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function destroy(\App\Models\Service $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
