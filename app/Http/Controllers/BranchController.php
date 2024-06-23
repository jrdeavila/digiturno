<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = \Illuminate\Support\Facades\Cache::remember('branches', 60, function () {
            return \App\Models\Branch::latest()->get();
        });
        return \App\Http\Resources\BranchResource::collection($branches);
    }

    public function store(\App\Http\Requests\BranchRequest $request)
    {
        $branch = \App\Models\Branch::create($request->all());
        return new \App\Http\Resources\BranchResource($branch);
    }

    public function show(\App\Models\Branch $branch)
    {
        return new \App\Http\Resources\BranchResource($branch);
    }

    public function update(\App\Http\Requests\BranchRequest $request, \App\Models\Branch $branch)
    {
        $branch->update($request->all());
        return new \App\Http\Resources\BranchResource($branch);
    }

    public function destroy(\App\Models\Branch $branch)
    {
        $branch->delete();
        return response()->json(null, 204);
    }
}
