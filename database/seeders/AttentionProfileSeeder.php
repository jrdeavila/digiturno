<?php

namespace Database\Seeders;

use App\Models\AttentionProfile;
use App\Models\Service;
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
            $a = AttentionProfile::create([
                'name' => $attentionProfile,
            ]);
            foreach ($services as $service) {
                $s = Service::where('name', $service)->firstOrFail();
                $a->services()->attach($s);
            }
        }
    }
}
