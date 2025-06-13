<?php

namespace App\Jobs;

use App\Models\Shift;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;
use League\Csv\Writer;

class GenerateShiftReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(Request $request): void
    {
        $shifts = Shift::query()
            ->when($request->get('branch_id'), function ($query) use ($request) {
                $query->whereHas('room', function ($query) use ($request) {
                    $query->where('branch_id', $request->get('branch_id'));
                });
            })
            ->when($request->get('room_id'), function ($query) use ($request) {
                $query->where('room_id', $request->get('room_id'));
            })
            ->when($request->get('attention_profile_id'), function ($query) use ($request) {
                $query->where('attention_profile_id', $request->get('attention_profile_id'));
            })
            ->when($request->get('state'), function ($query) use ($request) {
                $query->where('state', $request->get('state'));
            })
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->get('end_date'));
            })
            ->when($request->get('qualification'), function ($query) use ($request) {
                $query->whereHas('qualification', function ($query) use ($request) {
                    $query->where('qualification', $request->get('qualification'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = Writer::createFromString('');
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
            'Calificación',
            'Tiempo de Atención',
            'Creado En',
            'Actualizado En',
        ]);

        foreach ($shifts as $shift) {
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
                $shift->client?->name,
                $shift->client?->dni,
                $shift->client?->clientType->getTypeAttribute($shift->client->clientType->slug),
                $shift->state,
                $shift->qualification?->qualification,
                $timeToAttend,
                $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
                $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            ];
            $csv->insertOne($data);
        }
        // Save the file in the storage folder and public folder
        $filename = 'shifts-' . now()->setTimezone('America/Bogota')->format('Y-m-d-H-i-s') . '.csv';
        $path = public_path('storage/' . $filename);
        file_put_contents($path, $csv->getContent());
    }
}
