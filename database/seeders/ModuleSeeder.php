<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Room::all()->each(function ($room) {
            $attentionProfile = \App\Models\AttentionProfile::factory()->create();
            \App\Models\Module::factory(3)->create([
                'room_id' => $room->id,
                'client_type_id' => 1,
                'attention_profile_id' => $attentionProfile->id,
            ]);
            \App\Models\Module::factory(1)->create([
                'room_id' => $room->id,
                'client_type_id' => 2,
                'attention_profile_id' => $attentionProfile->id,
            ]);
        });

        \App\Models\Attendant::factory(10)->create();

        \App\Models\Module::all()->each(function (\App\Models\Module $module) {
            $module->attendants()->attach(\App\Models\Attendant::inRandomOrder()->first());
        });
    }
}
