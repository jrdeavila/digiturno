<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(5);
        return view('services.index', compact('services'));
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function create()
    {
        $services = Service::all();
        return view('services.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'service_id' => 'nullable|exists:services,id',
        ]);

        try {
            Service::create($request->all());
            return redirect()->route('services.index')->with('success', 'Servicio creado con exito.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'El servicio no pudo ser creado.');
        }
    }

    public function edit(Service $service)
    {
        $services = Service::all();
        return view('services.edit', compact('service', 'services'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required',
            'service_id' => 'nullable|exists:services,id',
        ]);

        try {
            $service->update($request->all());
            return redirect()->route('services.index')->with('success', 'Servicio actualizado con exito.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'El servicio no pudo ser actualizado.');
        }
    }

    public function destroy(Service $service)
    {
        try {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Servicio eliminado con exito.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'El servicio no pudo ser eliminado.');
        }
    }
}
