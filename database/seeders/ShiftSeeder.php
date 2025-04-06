<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = array_map('str_getcsv', file('database/seeders/data/shifts (6).csv'));
        // Ignorar la primera fila del csv
        array_shift($csv);

        DB::table('qualifications')->truncate();
        DB::table('shifts')->truncate();

        DB::BeginTransaction();

        foreach ($csv as $shift) {
            var_dump($shift);
            $client = \App\Models\Client::where('dni', $shift[5])->first();
            $module = \App\Models\Module::where('name', $shift[3])->first();
            $attendant = \App\Models\Attendant::where('name', "LIKE", $shift[9])->first();
            if ($module && $attendant) {
                \App\Models\ModuleAttendantAccess::create([
                    'module_id' => $module->id,
                    'attendant_id' => $attendant->id,
                ]);
            }
            // // Mapear timestamps

            $created_at = Carbon::createFromFormat('Y-m-d H:i A', $shift[11]);
            $updated_at = Carbon::createFromFormat('Y-m-d H:i A', $shift[12]);
            $shift = \App\Models\Shift::create([
                'client_id' => $client->id,
                'module_id' => $module?->id,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'attention_profile_id' => $module?->attention_profile_id ?? 5,
                'room_id' => $module?->room_id ?? 1,
                'state' => 'qualified',
            ]);

            $shift->qualification()->create([
                'qualification' => (function ($qualification) {
                    if ($qualification === "Excelente") {
                        return "excellent";
                    }
                    if ($qualification === "Bueno") {
                        return "good";
                    }
                    if ($qualification === "Regular") {
                        return "regular";
                    }
                    if ($qualification === "Malo") {
                        return "bad";
                    }
                    return "no_qualified";
                })($shift[8]),
            ]);

            $service = \App\Models\Service::where('name', $shift[1])->first();
            $other = \App\Models\Service::where('name', 'OTROS')->first();

            $service = $service ?? $other;

            $shift->services()->attach($service->id);
        }

        DB::commit();
    }
}
