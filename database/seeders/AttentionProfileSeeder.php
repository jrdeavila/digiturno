<?php

namespace Database\Seeders;

use App\Models\AttentionProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttentionProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttentionProfile::factory()->count(10)->create();
    }
}
