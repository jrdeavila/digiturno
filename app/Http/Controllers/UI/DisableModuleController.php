<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;

class DisableModuleController extends Controller
{
    public function __invoke(Module $module)
    {
        try {
            $module->enabled = false;
            $module->save();
            return redirect()->back()->with('success', 'El modulo ha sido desactivado con exito');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'El modulo no pudo ser desactivado');
        }
    }
}
