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
        \App\Models\Shift::factory(20)->create()->each(function ($shift) {
            $shift->qualification()->save(\App\Models\Qualification::factory()->make());
        });
    }
}
