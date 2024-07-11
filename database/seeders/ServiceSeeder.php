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
        $services = require __DIR__ . '/data/services.php';

        foreach ($services as $service) {
            $ap = \App\Models\Service::create([
                'name' => $service,
            ]);
        }
    }
}
