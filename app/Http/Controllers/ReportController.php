<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{

    public function __invoke()
    {
        $data = \App\Models\Shift::with('room', 'client', 'qualification', 'attentionProfile')->get();

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
            'Created At',
            'Updated At',
        ]);

        foreach ($data as $shift) {
            $csv->insertOne([
                $shift->id,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->module?->name,
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->slug,
                $shift->state,
                $shift->qualification?->qualification,
                $shift->created_at,
                $shift->updated_at,
            ]);
        }

        $filename = 'shifts.csv';

        Storage::put("temp/" . $filename, $csv->getContent());
        $filepath = storage_path("app/temp/" . $filename);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}
