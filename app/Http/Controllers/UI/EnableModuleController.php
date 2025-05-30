<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;

class EnableModuleController extends Controller
{
    public function __invoke(Module $module)
    {
        try {
            $module->enabled = true;
            $module->save();
            return redirect()->back()->with('success', 'El modulo ha sido activado con exito');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'El modulo no pudo ser activado');
        }
    }
}
