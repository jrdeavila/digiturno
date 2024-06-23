<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubserviceController extends Controller
{
    public function index(\App\Models\Service $service)
    {
        $subservices = \Illuminate\Support\Facades\Cache::remember("subservices.{$service->id}", 60, function () use ($service) {
            return $service->subservices;
        });
        return \App\Http\Resources\ServiceResource::collection($subservices);
    }
}
