<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ModuleType::create([
            'name' => 'Módulo de Atención',
        ]);

        \App\Models\ModuleType::create([
            'name' => 'Módulo de Seccional',
        ]);

        \App\Models\ModuleType::create([
            'name' => 'Módulo de Recepción',
        ]);

        \App\Models\ModuleType::create([
            'name' => 'Módulo de Pantalla',
        ]);
    }
}
