<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(5);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:branches,name',
            'address' => 'required|string',
        ]);
        try {
            DB::beginTransaction();
            Branch::create($request->all());
            DB::commit();
            return redirect()->route('branches.index')->with('success', 'La seccional fue creada con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('branches.index')->with('error', 'La seccional no pudo ser creada');
        }
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|unique:branches,name,' . $branch->id,
            'address' => 'required|string',
        ]);
        try {
            DB::beginTransaction();
            $branch->update($request->all());
            DB::commit();
            return redirect()->route('branches.index')->with('success', 'La seccional fue actualizada con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('branches.index')->with('error', 'La seccional no pudo ser actualizada');
        }
    }


    public function destroy(Branch $branch)
    {
        try {
            DB::beginTransaction();
            $branch->delete();
            DB::commit();
            return redirect()->route('branches.index')->with('success', 'La seccional fue eliminada con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('branches.index')->with('error', 'La seccional no pudo ser eliminada');
        }
    }
}
