<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateShiftReport;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use League\Csv\Writer;

class ShiftReportController extends Controller
{
    public function __invoke(Request $request)
    {
        try {

            $job = new GenerateShiftReport();
            $job->handle($request);
            return redirect()->back()->withInput()->with('success', 'El reporte sera generado en breve. Esta operacion puede tardar unos minutos.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'No se pudo generar el reporte');
        }
    }
}
