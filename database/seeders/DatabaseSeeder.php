<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            ModuleTypeSeeder::class,
            ClientTypeSeeder::class,
            ServiceSeeder::class,
            AttentionProfileSeeder::class,
            MontarSeccionalesConModulos::class,
            // ModuleSeeder::class,
            // RolePermissionSeeder::class,
            // RoomSeeder::class,
            // ShiftSeeder::class,
            AbsenceReasonSeeder::class,
        ]);
    }
}
