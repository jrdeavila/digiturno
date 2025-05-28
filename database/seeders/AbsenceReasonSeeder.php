<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbsenceReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AbsenceReason::create([
            'name' => 'Ir al baño',
        ]);

        \App\Models\AbsenceReason::create([
            'name' => 'Gestión interna',
        ]);

        \App\Models\AbsenceReason::create([
            'name' => 'Pausa activa',
        ]);

        \App\Models\AbsenceReason::create([
            'name' => 'Otro',
        ]);
    }
}
