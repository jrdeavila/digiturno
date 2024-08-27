<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class AttentionProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attentionProfiles = require __DIR__ . '/data/attentionProfiles.php';

        foreach ($attentionProfiles as $attentionProfile => $services) {
            $ap = \App\Models\AttentionProfile::create([
                'name' => $attentionProfile,
                // TODO: Add room_id to mount the relationship
            ]);

            foreach ($services as $service) {
                // Find or create the service
                $s = \App\Models\Service::firstOrCreate([
                    'name' => $service,
                    'room_id' => 1,
                ]);
                $ap->services()->attach($s);
            }
        }
    }
}
