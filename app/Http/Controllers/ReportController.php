<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $month = $request->get('month', null); // => 1
        $day = $request->get('day', null); // => 1
        $branchId = $request->get('branch_id', null);
        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month
        $data = \App\Models\Shift::when($month, function ($query, $month) {
            $month = (int) $month;
            $startDate = now()->month($month)->startOfMonth();
            $endDate = now()->month($month)->endOfMonth();
            return $query->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        })
            ->when($day, function ($query, $day) {
                $day = (int) $day;
                $date = Carbon::now()->day($day);
                return $query->whereDate('created_at', $date);
            })

            ->when($branchId, function ($query, $branchId) {
                return $query->whereHas('room', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })

            ->with('room', 'client', 'qualification', 'attentionProfile')->get();



        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Services',
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
            $servicesToString = $shift->services->map(function ($service) {
                return $service->name;
            })->implode('|');
            $csv->insertOne([
                $shift->id,
                $servicesToString,
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

        $filename = now()->month($month)->format('F') . '_shifts.csv';

        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }


    public function toJson(Request $request)
    {
        $month = $request->get('month', null); // => 1
        $day = $request->get('day', null); // => 1
        $branchId = $request->get('branch_id', null);
        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month
        $data = \App\Models\Shift::when($month, function ($query, $month) {
            $month = (int) $month;
            $startDate = now()->month($month)->startOfMonth();
            $endDate = now()->month($month)->endOfMonth();
            return $query->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        })
            ->when($day, function ($query, $day) {
                $day = (int) $day;
                $date = Carbon::now()->day($day);
                return $query->whereDate('created_at', $date);
            })

            ->when($branchId, function ($query, $branchId) {
                return $query->whereHas('room', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })

            ->with('room', 'client', 'qualification', 'attentionProfile')->get();



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
        $month = $request->get('month', null); // => 1
        $day = $request->get('day', null); // => 1
        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month
        $data = \App\Models\Shift::when($month, function ($query, $month) {
            $month = (int) $month;
            $startDate = now()->month($month)->startOfMonth();
            $endDate = now()->month($month)->endOfMonth();
            return $query->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        })
            ->when($day, function ($query, $day) {
                $day = (int) $day;
                $date = Carbon::now()->day($day);
                return $query->whereDate('created_at', $date);
            })


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
            $servicesToString = $shift->services->map(function ($service) {
                return $service->name;
            })->implode('|');
            $csv->insertOne([
                $shift->id,
                $servicesToString,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                // UTF-8 characters
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
        $month = $request->get('month', null); // => 1
        $day = $request->get('day', null); // => 1
        // If the day is not null, we will filter the data by the month and the day
        // Else, we will filter the data by the month
        $data = \App\Models\Shift::when($month, function ($query, $month) {
            $month = (int) $month;
            $startDate = now()->month($month)->startOfMonth();
            $endDate = now()->month($month)->endOfMonth();
            return $query->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        })
            ->when($day, function ($query, $day) {
                $day = (int) $day;
                $date = Carbon::now()->day($day);
                return $query->whereDate('created_at', $date);
            })
            ->where('attention_profile_id', 3)
            ->with('room', 'client', 'qualification', 'attentionProfile')->get();



        $dataMapped = [];

        foreach ($data as $shift) {
            $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
            $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
            $timeToAttend = floatval(number_format($timeToAttend, 2));
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
