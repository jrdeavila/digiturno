<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();

        User::create([
            'name' => 'Directora de Inspección, vigilancia y control',
            'email' => 'directordeinspeccion@ccvalledupar.org.co',
            'password' => bcrypt('ccv2024*')
        ]);

        User::create([
            'name' => 'Directora de Registros Públicos',
            'email' => 'jefederegistro@ccvalledupar.org.co',
            'password' => bcrypt('ccv2024*')
        ]);

        User::create([
            'name' => 'Administrador',
            'email' => 'desarrolladores@ccomerciodevalledupar.onmicrosoft.com',
            'password' => bcrypt('ccv2024*')
        ]);
    }
}
