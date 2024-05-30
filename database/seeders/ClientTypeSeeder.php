<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ClientType::create([
            'name' => 'Tramitador',
            'slug' => 'processor',
            'priority' => 2,
        ]);

        \App\Models\ClientType::create([
            'name' => 'Preferencial',
            'slug' => 'preferential',
            'priority' => 1,

        ]);

        \App\Models\ClientType::create([
            'name' => 'Estandar',
            'slug' => 'standard',
            'priority' => 3,

        ]);
    }
}
