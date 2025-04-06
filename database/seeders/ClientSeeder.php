<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Leer csv guardado en la carpeta database/seeders/data
        $csv = array_map('str_getcsv', file('database/seeders/data/August_shifts.csv'));

        // Recorrer el array de csv
        foreach ($csv as $client) {
            // Actualizar o crear un cliente
            \App\Models\Client::updateOrCreate([
                'dni' => $client[6],
            ], [
                'name' => $client[5],
                'client_type_id' => 3,
            ]);
            // \App\Models\Client::create([
            //     'name' => $client[5],
            //     'dni' => $client[6],
            //     'client_type_id' => 3,
            // ]);
        }

        // Agregar otro csv a la variable $csv
        $csv = array_map('str_getcsv', file('database/seeders/data/shifts (6).csv'));
        foreach ($csv as $client) {
            \App\Models\Client::updateOrCreate([
                'dni' => $client[5],
            ], [
                'name' => $client[4],
                'client_type_id' => 3,
            ]);
        }
    }
}
