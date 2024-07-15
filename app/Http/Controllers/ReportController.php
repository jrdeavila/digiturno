<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{

    public function __invoke()
    {
        $data = \App\Models\Shift::with('room', 'client', 'moduleAssignations', 'attentionProfile')->get();

        $csv = \League\Csv\Writer::createFromString('');

        $csv->insertOne([
            'ID',
            'Room Name',
            'Branch Name',
            'Client',
            'DNI',
            'Client Type',
            'State',
            'Qualification',
            'Created At',
            'Updated At',
        ]);

        foreach ($data as $shift) {
            $csv->insertOne([
                $shift->id,
                $shift->room->name,
                $shift->room->branch->name,
                $shift->client->name,
                $shift->client->dni,
                $shift->client->clientType->name,
                $shift->state,
                $shift->moduleAssignations->last()?->qualifications->last()->qualification ?? "no-assigned",
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
