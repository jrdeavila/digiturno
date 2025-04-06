<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Administrador
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        // Recepcionista
        \Spatie\Permission\Models\Role::create(['name' => 'receptionist']);
        // Atendedor
        \Spatie\Permission\Models\Role::create(['name' => 'attendant']);
        // Mostrador
        \Spatie\Permission\Models\Role::create(['name' => 'counter']);


        \App\Models\User::factory()->create([
            'name' => 'Administrador',
            'email' => 'administrador@camaradecomercio.com',
        ])->assignRole('admin');

        \App\Models\User::factory()->create([
            'name' => 'Recepcionista',
            'email' => 'recepcionista@camaradecomercio.com',
        ])->assignRole('receptionist');

        \App\Models\User::factory()->create([
            'name' => 'Atendedor',
            'email' => 'atendedor@camaradecomercio.com'
        ])->assignRole('attendant');

        \App\Models\User::factory()->create([
            'name' => 'Mostrador',
            'email' => 'mostrador@camaradecomercio.com'
        ])->assignRole('counter');
    }
}
