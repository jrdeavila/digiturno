<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attentionProfiles = require __DIR__ . '/data/services.php';

        foreach ($attentionProfiles as $key => $value) {
            $ap = \App\Models\Service::create([
                'name' => $key,
            ]);
            for ($i = 0; $i < count($value); $i++) {
                $ap->subservices()->create([
                    'name' => $value[$i]
                ]);
            }
        }
    }
}
