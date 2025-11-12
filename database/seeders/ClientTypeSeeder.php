<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientType::create([
            'id' => 1,
            'name' => 'Tramitador',
            'slug' => 'processor',
            'priority' => 3
        ]);

        ClientType::create([
            'id' => 2,
            'name' => 'Preferencial',
            'slug' => 'preferential',
            'priority' => 2

        ]);

        ClientType::create([
            'id' => 3,
            'name' => 'Afiliado',
            'slug' => 'affiliate',
            'priority' => 1
        ]);

        ClientType::create([
            'id' => 4,
            'name' => 'Estandar',
            'slug' => 'standard',
            'priority' => 4
        ]);
    }
}
