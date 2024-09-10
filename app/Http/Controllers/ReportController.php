<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $month = $request->get('month', now()->month);
        $data = \App\Models\Shift::whereBetween('created_at', [
            now()->month($month)->startOfMonth(),
            now()->month($month)->endOfMonth()
        ])->with('room', 'client', 'qualification', 'attentionProfile')->get();

        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Room Name',
            'Branch Name',
            'Module Name',
            'Client',
            'DNI',
            'Client Type',
            'Status',
            'Qualification',
            'Attendant',
            'TimeToAttend',
            'Created At',
            'Updated At',
        ]);

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = floatval(number_format($timeToAttend, 2));
            $csv->insertOne([
                $shift->id,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                $shift->state,
                $shift->qualification?->qualification,
                $attendant?->name,
                $timeToAttend,
                $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ]);
        }

        $filename = 'shifts.csv';

        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }


    public function toJson(Request $request)
    {
        $month = $request->get('month', now()->month);
        $data = \App\Models\Shift::whereBetween('created_at', [
            now()->month($month)->startOfMonth(),
            now()->month($month)->endOfMonth()
        ])->with('room', 'client', 'qualification', 'attentionProfile')->get();
        $dataMapped = [];

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = floatval(number_format($timeToAttend, 2));
            $dataMapped[] = [
                'ID' => $shift->id,
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
        $month = $request->get('month', now()->month);
        $month = (int) $month;
        $data = \App\Models\Shift::whereBetween('created_at', [
            now()->month($month)->startOfMonth(),
            now()->month($month)->endOfMonth()
        ])
            ->where('attention_profile_id', 3)
            ->with('room', 'client', 'qualification', 'attentionProfile')->get();
        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Service',
            'Room Name',
            'Branch Name',
            'Module Name',
            'Client',
            'DNI',
            'Client Type',
            'Status',
            'Qualification',
            'Attendant',
            'TimeToAttend',
            'Created At',
            'Updated At',
        ]);

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = floatval(number_format($timeToAttend, 2));
            $service = \App\Models\AttentionProfile::find($shift->attention_profile_id)->services->random();
            $csv->insertOne([
                $shift->id,
                $service->name,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
                $shift->state,
                $shift->qualification?->qualification,
                $attendant?->name,
                $timeToAttend,
                $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ]);
        }

        // Name of the month
        $filename = now()->month($month)->format('F') . '_shifts.csv';

        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function toCAEJson(Request $request)
    {
        $month = $request->get('month', now()->month);
        $month = (int) $month;
        $data = \App\Models\Shift::whereBetween('created_at', [
            now()->month($month)->startOfMonth(),
            now()->month($month)->endOfMonth()
        ])
            ->where('attention_profile_id', 3)
            ->with('room', 'client', 'qualification', 'attentionProfile')->get();
        $dataMapped = [];

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = floatval(number_format($timeToAttend, 2));
            $service = \App\Models\AttentionProfile::find($shift->attention_profile_id)->services->random();
            $dataMapped[] = [
                'ID' => $shift->id,
                'Service' => $service->name,
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
