<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = require __DIR__ . '/data/services.php';

        foreach ($services as $service => $subservices) {
            $s = Service::updateOrCreate([
                'name' => $service,
            ]);

            foreach ($subservices as $subservice) {
                $s->services()->create([
                    'name' => $subservice,
                    "service_id" => $s->id,
                ]);
            }
        }
    }
}
