<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\Room::create([
            'name' => 'Sala 1'
        ]);

        \App\Models\Room::create([
            'name' => 'Sala 2'
        ]);
    }
}
