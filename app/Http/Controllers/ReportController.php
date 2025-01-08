<?php

namespace App\Http\Controllers;

use App\Enums\ShiftSpanishLabel;
use App\Enums\ShiftState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private function convertShiftStatusToSpanish($status)
    {
        $status = ShiftState::from($status);
        $match =  match ($status) {
            ShiftState::Pending => ShiftSpanishLabel::Pending,
            ShiftState::PendingTransferred => ShiftSpanishLabel::PendingTransferred,
            ShiftState::Transferred => ShiftSpanishLabel::Transferred,
            ShiftState::InProgress => ShiftSpanishLabel::InProgress,
            ShiftState::Completed => ShiftSpanishLabel::Completed,
            ShiftState::Cancelled => ShiftSpanishLabel::Cancelled,
            ShiftState::Distracted => ShiftSpanishLabel::Distracted,
            ShiftState::Qualified => ShiftSpanishLabel::Qualified,
            ShiftState::Called => ShiftSpanishLabel::Called,
        };
        return $match->value;
    }

    private function convertUtf8($string)
    {
        $string = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'],
            $string
        );
        return $string;
    }

    private function generateFilename(Request $request, $filename)
    {
        $startDate = $request->get('start_date', null);
        $endDate = $request->get('end_date', null);
        if ($startDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
        }
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        }
        $filename = $startDate . '_' . $endDate . '_' . $filename;
        return $filename;
    }

    private function getShifts(Request $request)
    {
        $branchId = $request->get('branch_id', null);
        $startDate = $request->get('start_date', null);
        $endDate = $request->get('end_date', null);

        // Validar formato de fecha
        if ($startDate && !Carbon::parse($startDate, 'Y-m-d', 'es')->isValid()) {
            return response()->json([
                'message' => 'El formato de la fecha de inicio no es valido'
            ], 400);
        }

        if ($endDate && !Carbon::parse($endDate, 'Y-m-d', 'es')->isValid()) {
            return response()->json([
                'message' => 'El formato de la fecha de fin no es valido'
            ], 400);
        }

        // Validar fechas
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            if ($startDate->gt($endDate)) {
                return response()->json([
                    'message' => 'La fecha de inicio no puede ser mayor a la fecha de fin'
                ], 400);
            }
        }

        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month

        $data = \App\Models\Shift::whereIn('state', [
            ShiftState::Qualified,
            ShiftState::Transferred,
        ])

            ->when($startDate, function ($query, $startDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($branchId, function ($query, $branchId) {
                return $query->whereHas('room', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })


            ->with('room', 'client', 'qualification', 'attentionProfile')->get();

        return $data;
    }

    private function getShiftsByAttentionProfile(Request $request, $attentionProfileId)
    {
        $startDate = $request->get('start_date', null);
        $endDate = $request->get('end_date', null);
        $attentionProfileId = $request->get('attention_profile_id', null);

        // Validar formato de fecha

        if ($startDate && !Carbon::parse($startDate, 'Y-m-d', 'es')->isValid()) {
            return response()->json([
                'message' => 'El formato de la fecha de inicio no es valido'
            ], 400);
        }

        if ($endDate && !Carbon::parse($endDate, 'Y-m-d', 'es')->isValid()) {
            return response()->json([
                'message' => 'El formato de la fecha de fin no es valido'
            ], 400);
        }

        // Validar fechas

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            if ($startDate->gt($endDate)) {
                return response()->json([
                    'message' => 'La fecha de inicio no puede ser mayor a la fecha de fin'
                ], 400);
            }
        }


        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month
        $data = \App\Models\Shift::whereIn('state', [
            ShiftState::Qualified,
            ShiftState::Transferred,
        ])

            ->when($startDate, function ($query, $startDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                return $query->whereDate('created_at', '>=', $startDate);
            })

            ->when($endDate, function ($query, $endDate) {
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
                return $query->whereDate('created_at', '<=', $endDate);
            })

            ->when($attentionProfileId, function ($query, $attentionProfileId) {
                return $query->where('attention_profile_id', $attentionProfileId);
            })

            ->with('room', 'client', 'qualification', 'attentionProfile')->get();

        return $data;
    }



    public function __invoke(Request $request)
    {
        $data = $this->getShifts($request);
        $month = $request->get('month', null); // => 1
        $month = intval($month);

        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Servicios',
            'Sala',
            'Seccional',
            'Modulo',
            'Cliente',
            'Documento de Identidad',
            'Tipo de Cliente',
            'Estado',
            'Calificacion',
            'Funcionario',
            'Tiempo de Atencion',
            'Creado En',
            'Actualizado En',
        ]);

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = intval(number_format($timeToAttend, 2));
            $servicesToString = $shift->services->map(function ($service) {
                return $service->name;
            })->implode('|');
            $data = [
                $shift->id,
                $servicesToString,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                $this->convertShiftStatusToSpanish($shift->state),
                $shift->qualification?->qualification,
                $attendant?->name,
                $timeToAttend,
                $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ];
            $data = array_map([$this, 'convertUtf8'], $data);
            $csv->insertOne($data);
        }

        # Month name in Spanish and the year and the extension "_turnos.csv"
        $filename = $this->generateFilename($request, 'turnos.csv');
        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function toJson(Request $request)
    {
        $data = $this->getShifts($request);
        $dataMapped = [];

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = intval(number_format($timeToAttend, 2));
            $dataMapped[] = [
                'ID' => $shift->id,
                'Services' => $shift->services->map(function ($service) {
                    return $service->name;
                })->implode('|'),
                'Room Name' => $shift->room->name,
                'Branch Name' => $shift->room->branch->name,
                'Module Name' => $shift->module?->name,
                'Client' => $shift->client->name,
                'DNI' => $shift->client->dni,
                'Client Type' => $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                'Status' => $shift->state,
                'Qualification' => $shift->qualification?->qualification,
                'Attendant' => $attendant?->name,
                'TimeToAttend' => $timeToAttend,
                'Created At' => $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                'Updated At' => $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ];
        }
        return response()->json($dataMapped);
    }


    public function toCAE(Request $request)
    {
        $data = $this->getShiftsByAttentionProfile($request, 1);
        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Servicios',
            'Sala',
            'Seccional',
            'Modulo',
            'Cliente',
            'Documento de Identidad',
            'Tipo de Cliente',
            'Estado',
            'Calificacion',
            'Funcionario',
            'Tiempo de Atencion',
            'Creado En',
            'Actualizado En',
        ]);

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = intval(number_format($timeToAttend, 2));
            $servicesToString = $shift->services->map(function ($service) {
                return $service->name;
            })->implode('|');
            $data = [
                $shift->id,
                $servicesToString,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                // UTF-8 characters
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                $this->convertShiftStatusToSpanish($shift->state),
                $shift->qualification?->qualification,
                $attendant?->name,
                $timeToAttend,
                $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ];
            $data = array_map([$this, 'convertUtf8'], $data);
            $csv->insertOne($data);
        }

        // Month name in Spanish and the year and the extension "_turnos_cae.csv"
        // $monthName = Carbon::now()->month($month)->locale('es')->monthName;
        // $year = Carbon::now()->year;
        // $filename = $monthName . '_' . $year . '_turnos_cae.csv';
        $filename = $this->generateFilename($request, 'turnos_cae.csv');
        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function toCAEJson(Request $request)
    {
        $data = $this->getShiftsByAttentionProfile($request, 1);
        $dataMapped = [];
        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = intval(number_format($timeToAttend, 2));
            $servicesToString = $shift->services->map(function ($service) {
                return $service->name;
            })->implode('|');
            $dataMapped[] = [
                'ID' => $shift->id,
                'Service' => $servicesToString,
                'Room Name' => $shift->room->name,
                'Branch Name' => $shift->room->branch->name,
                'Module Name' => $shift->module?->name,
                'Client' => $shift->client->name,
                'DNI' => $shift->client->dni,
                'Client Type' => $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                'Status' => $shift->state,
                'Qualification' => $shift->qualification?->qualification,
                'Attendant' => $attendant?->name,
                'TimeToAttend' => $timeToAttend,
                'Created At' => $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                'Updated At' => $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ];
        }

        return response()->json($dataMapped);
    }
}
