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
            'id' => 1,
            'name' => 'Módulo de Atención',
            'use_qualification_module' => true,
        ]);

        \App\Models\ModuleType::create([
            'id' => 2,
            'name' => 'Módulo de Seccional',
            'use_qualification_module' => true,
        ]);

        \App\Models\ModuleType::create([
            'id' => 3,
            'name' => 'Módulo de Recepción',
            'use_qualification_module' => true,
        ]);

        \App\Models\ModuleType::create([
            'id' => 4,
            'name' => 'Módulo de Pantalla',
            'use_qualification_module' => false,
        ]);
    }
}
