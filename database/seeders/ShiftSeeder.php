<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Shift::factory(100)->create()->each(function ($shift) {
            $shift->moduleAssignations()->saveMany([
                \App\Models\ShiftModuleAssignation::factory()->make(),
                \App\Models\ShiftModuleAssignation::factory()->make(),
                \App\Models\ShiftModuleAssignation::factory()->make(),
            ]);
            $shift->moduleAssignations->each(function ($moduleAssignation) {
                $moduleAssignation->qualifications()->saveMany(
                    \App\Models\Qualification::factory(1)->make()
                );
            });
        });
    }
}
